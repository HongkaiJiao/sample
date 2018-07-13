<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class FollowersController extends Controller
{
    public function __construct()
    {
        // 请求过滤--只有登录后的用户才有权执行本控制器内的方法
        $this->middleware('auth');
    }

    // 关注功能
    public function store(User $user)
    {
        // 执行关注功能时若为登录用户本人，则直接跳转首页
        if (Auth::user()->id === $user->id) {
            return redirect('/');
        }
        // 当前用户是否已关注待执行关注操作的用户
        if (!Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }
        // 跳转用户个人信息页
        return redirect()->route('users.show',$user->id);
    }

    // 取关功能
    public function destroy(User $user)
    {
        if (Auth::user()->id === $user->id) {
            return redirect('/');
        }
        if (Auth::user()->isFollowing($user->id)) {
            Auth::user()->unfollow($user->id);
        }
        return redirect()->route('users.show',$user->id);
    }
}
