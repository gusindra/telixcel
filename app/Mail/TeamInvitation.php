<?php

namespace App\Mail;

use App\Models\TeamInvitation as ModelsTeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ModelsTeamInvitation $invitation)
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
        $url = URL::signedRoute('team.invitations.accept', [
            'invitation' => $this->invitation,
        ]);

        return $this->markdown('mail.team-invitation', [
            'acceptUrl' => $url
        ])->subject(__('Team Invitation'));
    }
}
