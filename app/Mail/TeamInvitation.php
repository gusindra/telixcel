<?php

namespace App\Mail;

use App\Models\TeamInvitation as ModelsTeamInvitation;
use Illuminate\Support\Facades\URL;
use Laravel\Jetstream\Mail\TeamInvitation as JetstreamTeamInvitation;

class TeamInvitation extends JetstreamTeamInvitation
{
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
