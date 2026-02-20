<div wire:poll.10s class="flex flex-wrap items-center justify-end gap-2 text-right" dir="rtl">
    @foreach($this->record->reactions_map->filter(fn($reaction) => collect($reaction)->get('reactions') > 0) as $reaction => $attribs)
    <x-filament::button x-on:click="$wire.react('{{ $reaction }}')" class="font-light"
        title="{{ $reaction }} {{ collect($attribs)->get('reactions') }} {{ str(collect($attribs)->get('name'))->plural(collect($attribs)->get('reactions')) }}"
        tooltip="{{ $reaction }} {{ collect($attribs)->get('reactions') }} {{ str(collect($attribs)->get('name'))->plural(collect($attribs)->get('reactions')) }}"
        :outlined="true" :color="collect($attribs)->get('meToo') ? 'primary' : 'gray'" size="xs">
        <span class="text-md">{{ $reaction }} {{ \Illuminate\Support\Number::forHumans(collect($attribs)->get('reactions'),
            maxPrecision: 2) }}</span>
    </x-filament::button>
    @endforeach
    <x-filament::dropdown placement="bottom-start">
        <x-slot name="trigger">
            <x-filament::button outlined color="gray" badge="+" size="xs"
                title="{{ __('nested-comments::nested-comments.reactions.add_reaction') }}"
                tooltip="{{ __('nested-comments::nested-comments.reactions.add_reaction') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                </svg>
            </x-filament::button>
        </x-slot>
        <div class="flex flex-wrap items-center justify-end gap-2 p-4 text-right" dir="rtl">
            @foreach($this->record->reactions_map as $reaction => $attribs)
            <x-filament::button x-on:click="$wire.react('{{$reaction}}')" @class([ 'font-light'=> true,
                ])
                :outlined="true"
                :color="collect($attribs)->get('meToo') ? 'primary' : 'gray'"
                size="md"
                title="{{collect($attribs)->get('name')}}"
                >
                <span class="text-lg">{{$reaction}}</span>
            </x-filament::button>
            @endforeach
        </div>
    </x-filament::dropdown>
</div>