<?php

namespace App\Mail;

use App\Models\RoleInvitation as ModelsRoleInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class RoleInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModelsRoleInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::signedRoute('role-invitations.accept', [
            'invitation' => $this->invitation,
        ]);

        Log::debug($url);
        return $this->markdown('mail.role-invitation', [
            'acceptUrl' => $url
        ])->subject(__('Role Invitation'));
    }
}
