<?php

namespace Hadialharbi\NestedComments\Concerns;

use Hadialharbi\NestedComments\Models\Reaction;
use Hadialharbi\NestedComments\NestedComments;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

use function config;

trait HasReactions
{
    public function reactions(): MorphMany
    {
        return $this->morphMany(config('nested-comments.models.reaction'), 'reactable');
    }

    public function getTotalReactionsAttribute(): int
    {
        return $this->reactions()->count();
    }

    public function getReactionsCountsAttribute()
    {
        return $this->reactions()->get(['id', 'emoji'])->groupBy('emoji')->map(function ($item) {
            return count($item);
        });
    }

    public function getEmojiReactorsAttribute()
    {
        return $this->reactions()->get(['id', 'emoji', 'user_id', 'guest_id', 'guest_name'])
            ->groupBy('emoji')
            ->map(function (Collection $item) {
                return $item->map(function ($reaction) {
                    return [
                        'id' => $reaction->getKey(),
                        'user_id' => $reaction->getAttribute('user_id'),
                        'guest_id' => $reaction->getAttribute('guest_id'),
                        'name' => $reaction->getAttribute('user_id') ? call_user_func(config('nested-comments.closures.getUserNameUsing'), $reaction->getAttribute('user')) : $reaction->getAttribute('guest_name'),
                    ];
                });
            });
    }

    public function getMyReactionsAttribute(): array
    {
        $allowGuest = config('nested-comments.allow-guest-reactions', false);

        if ($allowGuest && ! Auth::check()) {
            $guestId = app(NestedComments::class)->getGuestId();
            if (! $guestId) {
                return [];
            }

            return $this->reactions()
                ->where('guest_id', '=', $guestId)
                ->pluck('emoji')->values()->toArray();
        }

        return $this->reactions()
            ->where('user_id', '=', Auth::id())
            ->pluck('emoji')->values()->toArray();
    }

    public function iHaveReacted(string $emoji): bool
    {
        $myReactions = $this->getMyReactionsAttribute();
        if (empty($myReactions)) {
            return false;
        }

        return in_array($emoji, $myReactions);
    }

    /**
     * @throws Throwable
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
            throw new Exception('This reaction is not allowed.');
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
     * @throws Exception
     */
    protected function getExistingReaction(string $emoji): Reaction | Model | null
    {
        $allowMultiple = config('nested-comments.allow-multiple-reactions', false);
        $allowGuest = config('nested-comments.allow-guest-reactions', false);

        if (! $allowGuest && ! Auth::check()) {
            throw new Exception('You must be logged in to react.');
        }

        if ($allowGuest && ! Auth::check()) {
            $guestId = app(NestedComments::class)->getGuestId();
            if (! $guestId) {
                throw new Exception('Sorry, your guest session has not bee setup.');
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
        $allowOthers = config('nested-comments.allow-all-reactions', false);
        $allowed = config('nested-comments.allowed-reactions', []);
        if (empty($allowed)) {
            return true;
        }

        return $allowOthers || collect($allowed)->has($emoji);
    }

    public function getReactionsMapAttribute(): Collection
    {
        $reactions = collect($this->getReactionsCountsAttribute());
        $myReactions = collect($this->getMyReactionsAttribute())->mapWithKeys(fn ($value) => [$value => $value]);

        return collect(config('nested-comments.allowed-reactions', []))->mapWithKeys(function ($value, $emoji) use ($reactions, $myReactions) {
            $name = $value;

            return [$emoji => [
                'name' => $name,
                'emoji' => $emoji,
                'reactions' => $reactions->get($emoji),
                'meToo' => $myReactions->has($emoji),
            ]];
        });
    }
}
