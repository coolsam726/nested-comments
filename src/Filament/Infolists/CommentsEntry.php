<?php

namespace Hadialharbi\NestedComments\Filament\Infolists;

use Closure;
use Filament\Infolists\Components\Entry;

class CommentsEntry extends Entry
{
    protected bool | Closure $isLabelHidden = true;

    /** @phpstan-ignore-next-line */
    protected string $view = 'nested-comments::filament.infolists.comments-entry';

    protected array $columnSpan = ['default' => 'full'];
}
