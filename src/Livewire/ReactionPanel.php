<?php

namespace Hadialharbi\NestedComments\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ReactionPanel extends Component
{
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    public array $allReactions = [];

    public Model $record;

    public function mount(mixed $record = null): void
    {
        if (! $record?->getKey()) {
            throw new \Error('The Reactable $record property is required.');
        }
        $this->record = $record;
    }

    public function render()
    {
        return view('nested-comments::livewire.reaction-panel');
    }

    public function react($emoji): void
    {
        if (method_exists($this->record, 'react')) {
            $this->record->react($emoji);
            $this->dispatch('refresh')->self();
        }
    }
}
