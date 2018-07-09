<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
}
