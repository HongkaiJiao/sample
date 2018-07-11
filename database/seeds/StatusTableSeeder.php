<?php

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //为前三位用户创建100条微博测试数据
        $user_ids = ['1','2','3'];
        //使用app()方法获取一个Faker容器的实例，而后借助 randomElement() 方法来取出用户 id 数组中的任意一个元素并赋值给微博的 user_id
        $faker = app(Faker\Generator::class);

        $statuses = factory(Status::class)->times(100)->make()->each(function ($status) use ($faker, $user_ids) {
            $status->user_id = $faker->randomElement($user_ids);
        });
        Status::insert($statuses->toArray());
    }
}
