<?php

namespace Coolsam\NestedComments\Models;

use Coolsam\NestedComments\Concerns\HasReactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kalnoy\Nestedset\NodeTrait;

class Comment extends Model
{
    use HasReactions;
    use NodeTrait;

    protected $guarded = ['id'];

    public function commentable(): MorphTo
    {
        return $this->morphTo('commentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('nested-comments.models.user', config('auth.providers.users.model', 'App\\Models\\User')), 'user_id');
    }

    public function getCommentatorAttribute()
    {
        if ($this->user) {
            return call_user_func(config('nested-comments.closures.getUserNameUsing'), $this->user);
        }

        return $this->getAttribute('guest_name');
    }

    public function getRepliesCountAttribute(): int
    {
        return $this->children()->count();
    }
}
