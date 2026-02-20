<?php

namespace Hadialharbi\NestedComments\Filament\Tables\Actions;

use Filament\Tables\Actions\Action;
use Hadialharbi\NestedComments\NestedComments;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class CommentsAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->modalWidth('4xl')
            ->slideOver()
            ->modalHeading(fn (): string => __('nested-comments::nested-comments.comments.table.actions.view_comments.heading'));
        $this->modalSubmitAction(false);
        $this->modalCancelActionLabel(__('nested-comments::nested-comments.comments.table.actions.view_comments.close'));
        $this->icon('heroicon-o-chat-bubble-left-right');
        $this->modalIcon('heroicon-o-chat-bubble-left-right');
    }

    public static function getDefaultName(): ?string
    {
        return 'التعليقات';
    }

    public function getModalContent(): View | Htmlable | null
    {
        $record = $this->getRecord();

        return app(NestedComments::class)->renderCommentsComponent($record);
    }
}
