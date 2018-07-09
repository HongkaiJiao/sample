<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //times方法接受一个参数用于指定要创建的模型数量
        //make方法调用后将为模型创建一个集合
        $users = factory(User::class)->times(50)->make();
        //insert方法将生成的假用户列表数据批量插入到数据库中
        //makeVisible方法临时显示 User 模型里指定的隐藏属性$hidden
        User::insert($users->makeVisible(['password','remember_token'])->toArray());

        $user = User::find(1);
        $user->name = 'kevinjiao';
        $user->email = 'basketballjhk@163.com';
        $user->password = bcrypt('111111');
        $user->is_admin = true;
        $user->activated = true;
        $user->save();
    }
}
