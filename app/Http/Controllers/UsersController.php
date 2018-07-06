<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        /*给视图传参的多种方式
         *① view('users.show',compact('user'))
         *② view('users.show',['user'=>$user])
         *③ view('users.show')->with('user',$user)
        */
        return view('users.show',compact('user'));
    }

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
}
