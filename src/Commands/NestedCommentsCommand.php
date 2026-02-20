<?php

namespace Hadialharbi\NestedComments\Commands;

use Illuminate\Console\Command;

class NestedCommentsCommand extends Command
{
    public $signature = 'nested-comments';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
