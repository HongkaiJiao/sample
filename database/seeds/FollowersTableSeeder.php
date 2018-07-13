<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 获取所有用户
        $users = User::all();
        // 获取第一个用户
        $user = $users->first();
        // 第一个用户id
        $user_id = $user->id;

        // 获取去除掉 ID 为 1 的所有用户 ID 数组
        $followers = $users->slice($user_id); // slice方法返回集合中给定值后面的部分,若想限制返回内容的大小则将期望的大小作为第二个参数传递给方法
        $followers_ids = $followers->pluck('id')->toArray(); // pluck方法获取集合中给定键对应的所有值

        // 1 号用户关注除自身以外的所有用户
        $user->follow($followers_ids);

        // 除了 1 号用户以外的所有用户都来关注 1 号用户
        foreach ($followers as $follower) {
            $follower->follow($user_id);
        }
    }
}
