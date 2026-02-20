<?php

namespace Hadialharbi\NestedComments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hadialharbi\NestedComments\NestedComments
 */
class NestedComments extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hadialharbi\NestedComments\NestedComments::class;
    }
}
