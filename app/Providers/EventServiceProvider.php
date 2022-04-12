<?php

namespace App\Providers;

use App\Models\ApiCredential;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Request;
use App\Models\Template;
use App\Models\Ticket;
use App\Observers\ApiCredentialObserver;
use App\Observers\ClientObserver;
use App\Observers\RequestObserver;
use App\Observers\TemplateObserver;
use App\Observers\TicketObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Request::observe(RequestObserver::class);
        Template::observe(TemplateObserver::class);
        ApiCredential::observe(ApiCredentialObserver::class);
        Client::observe(ClientObserver::class);
        Ticket::observe(TicketObserver::class);
    }
}
