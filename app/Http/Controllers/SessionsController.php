<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            session()->flash('success','欢迎回来!');
            return redirect()->route('users.show',[Auth::user()]);
        } else {
            session()->flash('danger','很抱歉,您的邮箱和密码不匹配!');
            //使用withInput将已填数据带回，同时可剔除不希望带回显示的数据
            return redirect()->back()->withInput($request->except('password'));
        }
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success','您已成功退出!');
        return redirect('login');
    }
}
