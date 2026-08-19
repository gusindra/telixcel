<?php

namespace App\Http\Livewire\Ai\Concerns;

trait AuthorizesAiAdmin
{
    protected function authorizeAiAdmin(): void
    {
        abort_unless($this->isAiAdmin(), 403);
    }

    protected function isAiAdmin(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->super->first()?->role === 'superadmin') {
            return true;
        }

        return $user->activeRole
            && str_contains($user->activeRole->role->name ?? '', 'Admin');
    }

    protected function actorLabel(): string
    {
        $user = auth()->user();

        return $user ? ($user->name.' #'.$user->id) : 'unknown';
    }
}
