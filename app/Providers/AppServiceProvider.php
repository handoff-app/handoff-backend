<?php

namespace App\Providers;

use App\Contracts\Entities\Auth\JWT\Token;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(Token::class, \App\Entities\Auth\JWT\Token::class);
    }
}
