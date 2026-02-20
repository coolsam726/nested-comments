<div x-data wire:poll.15s>
    <div class="p-8 my-4 rounded-lg bg-gray-50 ring-gray-100 dark:bg-gray-950">
        <div class="flex flex-wrap items-center justify-between rtl:space-x-reverse">
            <div x-data="{ showFullDate: false }" class="flex items-center gap-2 rtl:flex-row">
                <x-filament::avatar :src="$this->getAvatar()" :alt="$this->getCommentator()" :name="$this->getCommentator()" size="md"
                    :circular="false" />
                <div x-on:mouseover="showFullDate = true" x-on:mouseout="showFullDate = false"
                    class="text-right cursor-pointer rtl:text-right">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $this->getCommentator() }}
                    </p>
                    <p x-show="!showFullDate" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $this->comment->created_at?->diffForHumans() }}
                    </p>
                    <p x-show="showFullDate" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $this->comment->created_at->format(
                            config(
                                'nested-comments.format-created-date',
                                'F j Y h:i:s
                                                                                                                        A',
                            ),
                        ) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="my-4 prose max-w-none dark:prose-invert">
            {!! e(new \Illuminate\Support\HtmlString($this->comment?->body)) !!}
        </div>
        <div class="flex flex-wrap items-center gap-2 md:space-x-4">
            <x-filament::link size="xs" class="cursor-pointer" icon="heroicon-s-chat-bubble-left-right"
                wire:click.prevent="toggleReplies">
                @if ($this->comment->replies_count > 0)
                    <span title="{{ \Illuminate\Support\Number::format($this->comment->replies_count) }}">
                        {{ \Illuminate\Support\Number::forHumans($this->comment->replies_count, maxPrecision: 3, abbreviate: true) }}
                        {{ str(__('nested-comments::nested-comments.comments.general.reply'))->plural($this->comment->replies_count) }}
                    </span>
                @else
                    <span title="{{ __('nested-comments::nested-comments.comments.general.no_replies') }}">
                        {{ __('nested-comments::nested-comments.comments.form.buttons.reply') }}
                    </span>
                @endif
            </x-filament::link>
            <livewire:nested-comments::reaction-panel :record="$this->comment" />
        </div>
    </div>
    @if ($replyingToCommentId === $comment->id)
        <div x-ref="repliesContainer" class="pb-4 pr-8 my-2 border-b border-r rounded-br-xl">

            {{-- فقط ردود هذا التعليق يتم عرضها --}}
            @foreach ($this->comment->children as $reply)
                <livewire:nested-comments::comment-card :key="$reply->getKey()" :comment="$reply" />
            @endforeach

            {{-- حقل الرد --}}
            <livewire:nested-comments::add-comment :key="'reply-' . $comment->getKey()" :commentable="$comment->commentable" :reply-to="$comment"
                :adding-comment="false" wire:loading.attr="disabled" />

            {{-- زر إخفاء --}}
            <x-filament::icon-button
                x-on:click="
                if ($refs.repliesContainer && $refs.repliesContainer.offsetHeight && $refs.repliesContainer.style.display !== 'none') {
                    const offset = $refs.repliesContainer.offsetHeight;
                    window.scrollBy({ top: -offset, behavior: 'smooth' });
                }
            "
                type="button" label="{{ __('nested-comments::nested-comments.comments.form.buttons.hide_replies') }}"
                tooltip="{{ __('nested-comments::nested-comments.comments.form.buttons.hide_replies') }}"
                icon="heroicon-o-minus-circle" class="absolute -left-8 -bottom-4" wire:click.prevent="toggleReplies" />
        </div>
    @endif

</div>

@script
    <script>
        document.querySelectorAll('[data-mention-id]').forEach(element => {
            // add an @ before using a pseudo-element
            element.classList.add(['comment-mention']);
        });
    </script>
@endscript
