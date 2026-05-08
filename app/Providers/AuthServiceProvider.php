<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Project;
use App\Models\Team;
use App\Models\Template;
use App\Policies\OrderPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TemplatePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        Project::class => ProjectPolicy::class,
        Team::class => TeamPolicy::class,
        Template::class => TemplatePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
