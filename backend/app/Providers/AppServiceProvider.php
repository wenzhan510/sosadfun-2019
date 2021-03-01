<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserInfo;
use App\Observers\UserObserver;
use App\Observers\UserInfoObserver;
use App\Models\RegistrationApplication;
use App\Observers\RegAppObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        User::observe(UserObserver::class);
        UserInfo::observe(UserInfoObserver::class);
        RegistrationApplication::observe(RegAppObserver::class);

        Relation::morphMap([
            'post' => 'App\Models\Post',
            'quote' => 'App\Models\Quote',
            'status' => 'App\Models\Status',
            'thread' => 'App\Models\Thread',
            'vote' => 'App\Models\Vote',
        ]);

        Validator::extend('display_length', 'App\Validators\DisplayLength@validate');
        Validator::extend('nocaptcha', 'App\Validators\NoCaptcha@validate');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()){
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
