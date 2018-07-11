<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Mail;

class UsersController extends Controller
{
    //中间件黑名单过滤使用except，白名单使用only
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['create','show','store','index','confirmEmail']
        ]);
        //只允许未登录用户访问注册页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    //显示注册页面
    public function create()
    {
        return view('users.create');
    }

    //显示个人信息页面
    public function show(User $user)
    {
        //模型关联后可使用$user->statuses()方式获取一个用户的所有微博
        $statuses = $user->statuses()
                         ->orderBy('created_at','desc')
                         ->paginate(30);

        /*给视图传参的多种方式
         *① view('users.show',compact('user'))
         *② view('users.show',['user'=>$user])
         *③ view('users.show')->with('user',$user)
        */
        return view('users.show',compact('user','statuses'));
    }

    //注册功能
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|min:6|max:20',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        /*该块内容移至激活功能中实现登录及重定向
         * Auth::login($user);//注册成功后自动登录
         * session()->flash('success','欢迎，你将在这里开启一段新的旅程，enjoy it~~');
         * return redirect()->route('users.show',[$user->id]);
         */
        //发送激活邮件并重定向至首页
        $this->sendEmailConfirmationTo($user);
        session()->flash('success','验证邮件已发送至您的注册邮箱，请注意查收。');
        return redirect('/');
    }

    //显示编辑页面
    public function edit(User $user)
    {
        //authorize方法用来验证用户授权策略(类似于can方法)--两个参数：授权策略的名称、进行授权验证的数据；无权限运行时会抛出HTTPException
        //authorize方法为控制器辅助函数
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

    //更新功能
    public function update(User $user,Request $request)
    {
        //表单验证，nullable--空白提交也可通过验证
        $this->validate($request,[
            'name' => 'required|min:6|max:20',
            'password' => 'nullable|confirmed|min:6'
        ]);
        //对更新操作进行授权验证
        $this->authorize('update',$user);
        //对用户对象进行更新
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        //更新成功的消息提醒
        session()->flash('success','个人资料更新成功!');
        //重定向至个人信息页面
        return redirect()->route('users.show',$user->id);
    }

    //显示所有用户列表页面
    public function index()
    {
        //$users = User::all();//取出全部用户，影响性能
        $users = User::paginate(10);//分页获取用户
        return view('users.index',compact('users'));
    }

    //删除用户功能
    public function destroy(User $user)
    {
        //对删除操作进行授权验证
        $this->authorize('destroy',$user);
        //使用Eloquent 模型提供的 delete 方法对用户资源进行删除
        $user->delete();
        session()->flash('success','成功删除用户!');
        return back();
    }

    //发送邮件至指定用户
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'basketballjhk@163.com';
        $name = 'kevinjiao';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";
        /*使用Mail接口的send方法来实现邮件发送，该方法接收三个参数：
         * ①包含邮件消息的视图名称，
         * ②要传递给该视图的数据数组,
         * ③接收邮件消息实例的闭包回调,在该回调中自定义邮件消息的发送者、接收者、邮件主题等信息
        Mail::send($view,$data,function ($message) use ($from,$name,$to,$subject) {
            $message->from($from,$name)->to($to)->subject($subject);
        });*/
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    //邮件激活功能
    public function confirmEmail($token)
    {
        //firstOrFail方法:取出第一个用户，在查询不到指定用户时将返回一个 404 响应
        $user = User::where('activation_token',$token)->firstOrFail();
        //设置用户激活状态等信息
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);//登录
        session()->flash('success','激活成功！你将在这里开启一段新的旅程，enjoy it~~');
        return redirect()->route('users.show',[$user->id]);
    }
}
