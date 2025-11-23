<?php

namespace App\Actions\Jetstream;

use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Mail\TeamInvitation;
use Laravel\Jetstream\TeamInvitation as TeamInvitationModel;

class ResendTeamInvitation
{
    /**
     * Resend the team invitation.
     */
    public function resend(TeamInvitationModel $invitation): void
    {
        Mail::to($invitation->email)->send(new TeamInvitation($invitation));
    }
}
