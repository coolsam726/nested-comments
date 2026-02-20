<?php

namespace Hadialharbi\NestedComments\Livewire;

use Error;
use Filament\Support\Concerns\EvaluatesClosures;
use Hadialharbi\NestedComments\Models\Comment;
use Hadialharbi\NestedComments\NestedCommentsServiceProvider;
use Livewire\Component;

class CommentCard extends Component
{
    use EvaluatesClosures;

    public bool $isEditing = false;

    public ?string $editedBody = null;

    public ?Comment $comment = null;

    // public bool $showReplies = false;

    public ?string $userAvatar = null;

    public ?string $userName = null;

    public ?int $replyingToCommentId = null;

    protected $listeners = [
        'refresh' => 'refreshReplies',
    ];

    public function mount(?Comment $comment = null): void
    {
        if (! $comment) {
            throw new Error('The $comment property is required.');
        }

        $this->comment = $comment;
        $this->editedBody = $comment->body;

    }

    public function render()
    {
        $namespace = NestedCommentsServiceProvider::$viewNamespace;

        return view("$namespace::livewire.comment-card");
    }

    public function refreshReplies(): void
    {
        $this->comment = $this->comment?->refresh();
    }

    public function toggleReplies(): void
    {
        $this->replyingToCommentId = ($this->replyingToCommentId === $this->comment->id) ? null : $this->comment->id;
    }

    public function getAvatar()
    {
        if (! $this->comment) {
            return '';
        }

        /**
         * @phpstan-ignore-next-line
         */
        return $this->comment->commentable?->getUserAvatarUsing($this->comment);
    }

    public function getCommentator(): string
    {
        if (! $this->comment) {
            return '';
        }

        /**
         * @phpstan-ignore-next-line
         */
        return $this->comment->commentable?->getUserNameUsing($this->comment);
    }

    public function updateComment(): void
    {
        if (! auth()->check() || auth()->id() !== $this->comment->user_id) {
            abort(403);
        }

        $this->validate([
            'editedBody' => 'required|string|min:1',
        ]);

        $this->comment->update(['body' => $this->editedBody]);
        $this->isEditing = false;
    }

    public function deleteComment(): void
    {
        if (! auth()->check() || auth()->id() !== $this->comment->user_id) {
            abort(403);
        }

        $this->comment->delete();
        $this->dispatch('refresh')->to(Comments::class);
    }

    public function enableEditing(): void
    {
        $this->isEditing = true;
    }

    public function cancelEditing(): void
    {
        $this->isEditing = false;
        $this->editedBody = $this->comment->body;
    }
}
