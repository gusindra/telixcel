<?php

namespace App\Providers;

use App\Listeners\UpdateCurrentRole;
use App\Models\ApiCredential;
use App\Models\Attachment;
use App\Models\Billing;
use App\Models\BlastMessage;
use App\Models\Client;
use App\Models\Commision;
use App\Models\Contract;
use App\Models\FlowProcess;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Request;
use App\Models\SaldoUser;
use App\Models\Template;
use App\Models\Ticket;
use App\Observers\ApiCredentialObserver;
use App\Observers\ApprovalObserver;
use App\Observers\AttachmentObserver;
use App\Observers\BillingObserver;
use App\Observers\ClientObserver;
use App\Observers\ContractObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\RequestObserver;
use App\Observers\TemplateObserver;
use App\Observers\TicketObserver;
use App\Observers\ProjectObserver;
use App\Observers\QuotationObserver;
use App\Observers\SaldoUserObserver;
use App\Observers\SmsBlastObserver;
use App\Observers\CommissionObserver;
use Laravel\Jetstream\Events\TeamUpdated;

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
        TeamUpdated::class => [
            UpdateCurrentRole::class,
        ]
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
        Project::observe(ProjectObserver::class);
        Quotation::observe(QuotationObserver::class);
        SaldoUser::observe(SaldoUserObserver::class);
        BlastMessage::observe(SmsBlastObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Order::observe(OrderObserver::class);
        OrderProduct::observe(OrderProductObserver::class);
        Contract::observe(ContractObserver::class);
        Commision::observe(CommissionObserver::class);
        FlowProcess::observe(ApprovalObserver::class);
        Billing::observe(BillingObserver::class);
    }
}
