<?php

namespace Hadialharbi\NestedComments\Concerns;

use Hadialharbi\NestedComments\Models\Comment;
use Hadialharbi\NestedComments\NestedComments;
use Exception;
use FilamentTiptapEditor\Data\MentionItem;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

use function auth;

/**
 * @mixin Model
 */
trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(config('nested-comments.models.comment'), 'commentable');
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->comments()
            ->where('parent_id', '=', null)
            ->count();
    }

    public function getCommentsTree($offset = null, $limit = null, $columns = ['*']): Collection
    {
        $query = $this->comments()
            ->getQuery()
            ->where('parent_id', '=', null);
        if (filled($offset)) {
            $query->offset($offset);
        }
        if (filled($limit)) {
            $query->limit($limit);
        }

        if (filled($columns) && $columns[0] !== '*') {
            $columns = ['id', 'parent_id', '_lft', '_rgt', ...$columns];
        }

        return $query->get($columns)->map(function (Comment $comment) use ($columns) {
            $descendants = $comment->getDescendants($columns);
            $comment->setAttribute('replies', $descendants);

            return $comment;
        });
    }

    /**
     * @throws Exception
     */
    public function comment(string $comment, mixed $parentId = null, ?string $name = null)
    {
        $allowGuest = config('nested-comments.allow-guest-comments', false);
        if (! $allowGuest && ! auth()->check()) {
            throw new Exception('You must be logged in to comment.');
        }
        if ($name) {
            app(NestedComments::class)->setGuestName($name);
        }
        $guestId = app(NestedComments::class)->getGuestId();
        $guestName = app(NestedComments::class)->getGuestName();
        if ($allowGuest && ! auth()->check()) {
            $userId = null;
        } else {
            $userId = auth()->id();
        }

        return $this->comments()->create([
            'user_id' => $userId,
            'body' => $comment,
            'commentable_id' => $this->getKey(),
            'commentable_type' => $this->getMorphClass(),
            'parent_id' => $parentId,
            'guest_id' => $guestId,
            'guest_name' => $guestName,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function editComment(Comment $comment, string $body, ?string $name = null): ?bool
    {
        $allowGuest = config('nested-comments.allow-guest-comments', false);

        if (! auth()->check() && ! $allowGuest) {
            throw new Exception('You must be logged in to edit your comment.');
        }

        if ($name) {
            app(NestedComments::class)->setGuestName($name);
        }

        if (auth()->check() && $comment->getAttribute('user_id') !== auth()->id()) {
            throw new Exception('You are not authorized to edit this comment.');
        }

        if ($allowGuest && ! auth()->check()) {
            $guestId = app(NestedComments::class)->getGuestId();
            if ($comment->getAttribute('guest_id') !== $guestId) {
                throw new Exception('You are not authorized to edit this comment.');
            }
        }
        $guestName = app(NestedComments::class)->getGuestName();

        return $comment->update(['body' => $body, 'guest_name' => $guestName, 'ip_address' => request()->ip()]);
    }

    /**
     * @throws Exception
     */
    public function deleteComment(Comment $comment): ?bool
    {
        $allowGuest = config('nested-comments.allow-guest-comments', false);

        if (! auth()->check() && ! $allowGuest) {
            throw new Exception('You must be logged in to delete your comment.');
        }

        if (auth()->check() && $comment->getAttribute('user_id') !== auth()->id()) {
            throw new Exception('You are not authorized to delete this comment.');
        }

        if ($allowGuest && ! auth()->check()) {
            $guestId = app(NestedComments::class)->getGuestId();
            if ($comment->getAttribute('guest_id') !== $guestId) {
                throw new Exception('You are not authorized to delete this comment.');
            }
        }

        return $comment->delete();
    }

    final public function getUserNameUsing(Comment $comment): string
    {
        return $this->getUserName($comment->getAttribute('user'));
    }

    final public function getUserAvatarUsing(Comment $comment): ?string
    {
        $user = $comment->user ?? $comment->guest_name ?? __('nested-comments::nested-comments.comments.general.guest');

        return $this->getUserAvatar($user);
    }

    public function getUserName(Model | Authenticatable | null $user): string
    {
        return app(NestedComments::class)->getUserName($user);
    }

    public function getUserAvatar(Model | Authenticatable | string | null $user): ?string
    {
        return app(NestedComments::class)->getDefaultUserAvatar($user);
    }

    /**
     * @return array<int, MentionItem>
     */
    public function getMentionsUsing(string $query): array
    {
        return $this->getMentionsQuery($query)
//            ->where('username', 'like', "%{$query}%")
            ->take(50)
            ->get()
            ->map(function ($user) {
                return new MentionItem(
                    id: $user->getKey(),
                    label: $this->getUserName($user),
                    image: $this->getUserAvatar($user),
                    roundedImage: true,
                );
            })->toArray();
    }

    public function getMentionsQuery(string $query): Builder
    {
        return app(NestedComments::class)->getUserMentionsQuery($query);
    }
}
