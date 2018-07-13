<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*过滤用户提交的字段，只有包含在该属性中的字段才能够被正常更新，避免批量赋值的错误*/
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /*该字段在需要对用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏*/
    protected $hidden = [
        'password', 'remember_token',
    ];

    //头像函数
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //boot方法在用户模型类完成初始化之后进行加载--对事件的监听需要放在该方法中
    public static function boot()
    {
        parent::boot();
        //creating方法用于监听模型被创建之前的事件
        static::creating(function ($user) {
            $user->activation_token = str_random(30);//在用户创建(注册)之前生成激活令牌
        });
    }

    //修改用于向用户发送密码重置链接的通知类--自定义用于重置的邮件
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    //定义模型关联函数，指明一个用户拥有多条微博
    public function statuses()
    {
        // hasMany('App\要引入的实体类名','另一表中的外键','本表中的主键');别表的外键与本表的主键不同要自己添加第二第三个参数
        // Status::class 返回类Status的完全限定名称
        return $this->hasMany(Status::class);
    }

    //所有关注用户的微博动态数据
    public function feed()
    {
        //return $this->statuses()->orderBy('created_at','desc');//从数据库中取出当前用户发布过的所有微博

        // 通过 followings 方法取出所有关注用户的信息，再借助 pluck 方法将 id 进行分离并赋值给 user_ids
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids,Auth::user()->id);
        // 使用 Eloquent 关联的 预加载 with 方法，预加载避免了 N+1 查找的问题，提高查询效率
        return Status::whereIn('user_id',$user_ids)->with('user')->orderBy('created_at','desc');
    }

    /**
     * followers 表
     * id     user_id     follower_id
     * 1       2             3         // 用户3关注了用户2。也就是说用户3是用户2 的粉丝。
     * 2       4             2         // 用户2关注了用户4。也就是说用户2是用户4的粉丝。
     * 3       3             2         // 和第一条相反。两人互相关注。 用户2也是用户3的粉丝。
     *
     * belongsToMany(1,2,3,4)
     * 四个参数意思：
     *  1、目标model的class全称呼。
     *  2、中间表名
     *  3、中间表中当前model对应的关联字段
     *  4、中间表中目标model对应的关联字段
     *
     *   获取粉丝：（重点：这里粉丝也是用户。所以就把User 模型也当粉丝模型来用）
     *  eg: belongsToMany(User::class,'followers','user_id','follower_id');
     *      粉丝表,中间表,当前model在中间表中的字段,目标model在中间表中的字段。
     */
    public function followers()
    { //获取粉丝关系列表
        //即在followers表中以user_id为主来获取follower_id
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }

    /**
     *用户关注人列表
     * 关注人列表，关联表，当前model在中间表中的字段，目标model在中间表中的字段。
     */
    public function followings()
    { //获取用户关注人列表
        //即在followers表中以follower_id为主来获取user_id
        return $this->belongsToMany(User::class,'followers','follower_id','user_id');
    }

    //关注功能
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }

    //取关功能
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //当前用户是否关注了$user_id
    public function isFollowing($user_id)
    {
        /* $this->followings相当于$this->followings()->get(),返回的是Collection类的实例，而contains方法是Collection类的一个方法,
         * $this->followings()返回的是Relation类，并没有contains方法
        */
        return $this->followings->contains($user_id);
    }
}
