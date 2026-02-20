<?php

namespace Hadialharbi\NestedComments\Livewire;

use Hadialharbi\NestedComments\Concerns\HasComments;
use Hadialharbi\NestedComments\Models\Comment;
use Hadialharbi\NestedComments\NestedComments;
use Hadialharbi\NestedComments\NestedCommentsServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

class Comments extends Component
{
    /**
     * @var (Model&HasComments)|null
     */
    public ?Model $record = null;

    protected $listeners = [
        'refresh' => 'refreshComments',
    ];

    /**
     * @var Collection<Comment>
     */
    public Collection $comments;

    public function mount(): void
    {
        $this->comments = collect();

        if (! $this->record) {
            throw new \Error('Record model (Commentable) is required');
        }

        if (! app(NestedComments::class)->classHasTrait($this->record, HasComments::class)) {
            throw new \Error('Record model must use the HasComments trait');
        }

        $this->refreshComments();
    }

    public function refreshComments(): void
    {
        $this->record = $this->record->refresh();
        if (method_exists($this->record, 'getCommentsTree')) {
            $this->comments = $this->record->getCommentsTree();
        }
    }

    public function render()
    {
        $namespace = NestedCommentsServiceProvider::$viewNamespace;

        return view($namespace . '::livewire.comments');
    }
}
