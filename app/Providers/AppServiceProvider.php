<?php

namespace App\Providers;

use App\Services\EmailService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {
        $this->app->singleton(EmailService::class, function($app) {
            return new EmailService();
        });
    }

    public function boot(): void {
        Event::listen(function(\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('laravelpassport', \SocialiteProviders\LaravelPassport\Provider::class);
        });
    }
}
