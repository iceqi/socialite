<?php

namespace RingoProject\Socialite;

use Laravel\Socialite\Contracts\Factory;

class SocialiteServiceProvider extends \Laravel\Socialite\SocialiteServiceProvider
{
    public function register()
    {
        $this->app->singleton(Factory::class, function ($app) {
            return new SocialiteManager($app);
        });
    }
}