<?php
/*框架的核心文件，在 Laravel 启动时，会最先加载该文件*/
namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Carbon 是 PHP DateTime 的一个简单扩展，Laravel 将其默认集成到了框架中
        Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
