<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    //中间件黑名单过滤使用except，白名单使用only
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['create','show','store','index']
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
        /*给视图传参的多种方式
         *① view('users.show',compact('user'))
         *② view('users.show',['user'=>$user])
         *③ view('users.show')->with('user',$user)
        */
        return view('users.show',compact('user'));
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
        Auth::login($user);//注册成功后自动登录
        session()->flash('success','欢迎，你将在这里开启一段新的旅程，enjoy it~~');
        return redirect()->route('users.show',[$user->id]);
    }

    //显示编辑页面
    public function edit(User $user)
    {
        //验证用户授权策略--两个参数：授权策略的名称、进行授权验证的数据；无权限运行时会抛出HTTPException
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
}
