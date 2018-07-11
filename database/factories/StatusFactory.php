<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Status::class, function (Faker $faker) {
    $date_time = $faker->date . ' ' . $faker->time;//拼接测试数据的时间
    return [
        'content'    => $faker->text(),
        'created_at' => $date_time,
        'updated_at' => $date_time,
    ];
});
