<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        //只允许未登录用户访问登录页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
    }

    //登录页面显示
    public function create()
    {
        return view('sessions.create');
    }

    //登录功能
    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials,$request->has('remember'))) {
            session()->flash('success','欢迎回来!');
            //intended方法：重定向至上一次请求尝试访问的页面，并接受一个默认跳转地址为参数
            return redirect()->intended(route('users.show',[Auth::user()]));
        } else {
            session()->flash('danger','很抱歉,您的邮箱和密码不匹配!');
            //使用withInput将已填数据带回，同时可剔除不希望带回显示的数据
            return redirect()->back()->withInput($request->except('password'));
        }
    }

    //退出功能
    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出!');
        return redirect('login');
    }
}
