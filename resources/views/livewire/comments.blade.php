<x-filament::section wire:poll.10s :compact="true" class="!ring-0 !shadow-none !p-0">
    @if(config('nested-comments.show-heading', true))
    <x-slot name="heading">
        <div class="flex items-center space-x-2 space-x-reverse">
            <div>
                {{ __('nested-comments::nested-comments.comments.general.comments') }}
            </div>
            @if(config('nested-comments.show-badge-counter', true))
            <div>
                <x-filament::badge :color="config('nested-comments.badge-counter-color', 'danger')"
                    :title="$this->comments->count()">
                    {{\Illuminate\Support\Number::forHumans($this->comments->count(),maxPrecision: 3, abbreviate:
                    true)}}
                </x-filament::badge>
            </div>
            @endif
        </div>
    </x-slot>
    @endif
    @if(config('nested-comments.show-refresh-button', true))
    <x-slot name="headerEnd">
        <x-filament::button wire:click.prevent="refreshComments()"
            :color="config('nested-comments.color-refresh-button')"
            :icon="config('nested-comments.icon-refresh-button')"
            :outlined="config('nested-comments.outlined-refresh-button')"
            :tooltip="config('nested-comments.style-refresh-button', 'button') === 'icon' ? __('nested-comments::nested-comments.comments.form.buttons.refresh') : null">
            @if(config('nested-comments.style-refresh-button', 'button') === 'button')
            {{ __('nested-comments::nested-comments.comments.form.buttons.refresh') }}
            @endif
        </x-filament::button>
    </x-slot>
    @endif
    @foreach($this->comments as $comment)
    <livewire:nested-comments::comment-card :key="$comment->getKey()" :comment="$comment" />
    @endforeach
    <livewire:nested-comments::add-comment :commentable="$this->record" />
</x-filament::section>