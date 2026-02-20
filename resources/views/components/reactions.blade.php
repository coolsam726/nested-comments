@props([
    'record'
])
@if(isset($record))
    @if(app(\Hadialharbi\NestedComments\NestedComments::class)->classHasTrait($record, \Hadialharbi\NestedComments\Concerns\HasReactions::class))
        <livewire:nested-comments::reaction-panel :record="$record"/>
    @else
        <p>{{ __('nested-comments::nested-comments.comments.general.record_is_not_configured_for_reactions') }}</p>
    @endif
@endif
