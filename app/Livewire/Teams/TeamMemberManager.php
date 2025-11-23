<?php

namespace App\Livewire\Teams;

use App\Actions\Jetstream\ResendTeamInvitation;
use Laravel\Jetstream\Http\Livewire\TeamMemberManager as JetstreamTeamMemberManager;
use Laravel\Jetstream\TeamInvitation;

class TeamMemberManager extends JetstreamTeamMemberManager
{
    /**
     * Resend a team invitation.
     */
    public function resendTeamInvitation(int $invitationId): void
    {
        $invitation = TeamInvitation::findOrFail($invitationId);

        app(ResendTeamInvitation::class)->resend($invitation);

        $this->dispatch('saved');

        session()->flash('flash.banner', 'Invitation resent successfully.');
        session()->flash('flash.bannerStyle', 'success');
    }
}
