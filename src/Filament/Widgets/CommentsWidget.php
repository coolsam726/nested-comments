<?php

namespace Hadialharbi\NestedComments\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class CommentsWidget extends Widget
{
    public ?Model $record = null;

    /** @phpstan-ignore-next-line */
    protected static string $view = 'nested-comments::filament.widgets.comments-widget';

    protected int | string | array $columnSpan = 'full';
}
