<?php

namespace Hadialharbi\NestedComments\Livewire;

use Hadialharbi\NestedComments\Concerns\HasComments;
use Hadialharbi\NestedComments\Models\Comment;
use Hadialharbi\NestedComments\NestedCommentsServiceProvider;
use Error;
use Exception;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use FilamentTiptapEditor\Concerns\HasFormMentions;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * @property Form $form
 */
class AddComment extends Component implements HasForms
{
    use HasFormMentions;
    use InteractsWithForms;

    public ?array $data = [];

    public bool $addingComment = false;

    public ?Model $commentable = null;

    public ?Comment $replyTo = null;

    public function mount(?Model $commentable, ?Comment $replyTo): void
    {
        if (! $commentable) {
            throw new Error('The $commentable property is required.');
        }
        $this->commentable = $commentable;
        $this->replyTo = $replyTo;
        $this->form->fill();
    }

    public function getCommentable(): Model
    {
        if (! $this->commentable) {
            throw new Error('The $commentable property is required.');
        }

        return $this->commentable;
    }

    public function form(Form $form): Form
    {
        /**
         * @var Model<HasComments>|HasComments $commentable
         *
         * @phpstan-ignore-next-line
         */
        $commentable = $this->getCommentable();

        return $form
            ->schema([
                TiptapEditor::make('body')
                    ->label(__('nested-comments::nested-comments.comments.form.field.comment.label'))
                    ->profile('minimal')
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->mentionItemsPlaceholder(__('nested-comments::nested-comments.comments.form.field.comment.mention_items_placeholder'))
                    ->emptyMentionItemsMessage(__('nested-comments::nested-comments.comments.form.field.comment.empty_mention_items_message'))
                    /**
                     * @phpstan-ignore-next-line
                     */
                    ->getMentionItemsUsing(fn (string $query) => $commentable->getMentionsUsing($query))
                    ->maxContentWidth('full')
                    ->required()
                    ->autofocus(),
            ])
            ->statePath('data')
            ->model(config('nested-comments.models.comment', Comment::class));
    }

    /**
     * @throws Exception
     */
    public function create(): void
    {
        $data = $this->form->getState();

        /**
         * @var Model<HasComments>|HasComments $commentable
         *
         * @phpstan-ignore-next-line
         */
        $commentable = $this->getCommentable();

        /**
         * @phpstan-ignore-next-line
         */
        $record = $commentable->comment(comment: $data['body'], parentId: $this->replyTo?->getKey());
        $this->dispatch('refresh');
        $this->showForm(false);
    }

    public function render(): View
    {
        $namespace = NestedCommentsServiceProvider::$viewNamespace;

        return view("$namespace::livewire.add-comment");
    }

    public function showForm(bool $adding): void
    {
        $this->form->fill();
        $this->addingComment = $adding;
    }
}
