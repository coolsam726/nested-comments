<?php

namespace Coolsam\NestedComments\Concerns;

use Coolsam\NestedComments\Models\Reaction;
use Coolsam\NestedComments\NestedComments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait HasReactions
{
    public function reactions(): MorphMany
    {
        return $this->morphMany(config('nested-comments.models.reaction'), 'reactable');
    }

    public function getReactionsCountAttribute(): int
    {
        return $this->reactions()->count();
    }

    /**
     * @throws \Throwable
     */
    public function react(string $emoji): Reaction | Model | int
    {
        $existing = $this->getExistingReaction($emoji);
        if ($existing) {
            $id = $existing->getKey();
            $existing->deleteOrFail();

            return $id;
        }
        if (! $this->isAllowed($emoji)) {
            throw new \Exception('This reaction is not allowed.');
        }

        return $this->reactions()->create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'emoji' => $emoji,
            'guest_id' => app(NestedComments::class)->getGuestId(),
            'guest_name' => app(NestedComments::class)->getGuestName(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function getExistingReaction(string $emoji): Reaction | Model | null
    {
        $allowMultiple = \config('nested-comments.allow-multiple-reactions', false);
        $allowGuest = \config('nested-comments.allow-guest-reactions', false);

        if (! $allowGuest && ! Auth::check()) {
            throw new \Exception('You must be logged in to react.');
        }

        if ($allowGuest && ! Auth::check()) {
            $guestId = app(NestedComments::class)->getGuestId();
            if (! $guestId) {
                throw new \Exception('Sorry, your guest session has not bee setup.');
            }
            $existingQuery = $this->reactions()
                ->where('guest_id', '=', $guestId);

        } else {
            $existingQuery = $this->reactions()
                ->where('user_id', '=', Auth::id());
        }

        if ($allowMultiple) {
            $existingQuery->where('emoji', '=', $emoji);
        }

        return $existingQuery->first();
    }

    public function isAllowed(string $emoji): bool
    {
        $allowed = config('nested-comments.allowed-reactions', []);
        if (empty($allowed)) {
            return true;
        }

        return in_array($emoji, $allowed);
    }
}
