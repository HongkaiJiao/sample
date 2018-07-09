<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * 用户更新时的权限验证--调用时无需传递当前登录用户实例，框架默认自动加载当前登录用户
     * @param $curUser --当前登录用户实例
     * @param $user    --要进行授权的用户实例
     * @return         --通过授权并进行下一操作 or 抛出403异常信息并拒绝访问
     */
    public function update(User $curUser,User $user)
    {
        return $curUser->id === $user->id;
    }

    //删除用户时的权限验证
    public function destroy(User $curUser,User $user)
    {
        return $curUser->is_admin && $curUser->id !== $user->id;
    }
}
