<?php

namespace Coolsam\NestedComments;

use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Color\Rgb;

class NestedComments
{
    const GUEST_ID_FIELD = 'guest_commentator_id';

    const GUEST_NAME_FIELD = 'guest_commentator_name';

    public function getGuestId(): ?string
    {
        return session(self::GUEST_ID_FIELD);
    }

    public function getGuestName(): string
    {
        if (Auth::check()) {
            return call_user_func(config('nested-comments.closures.getUserNameUsing', fn (Authenticatable | Model $user) => $user->getAttribute('name')), Auth::user());
        }

        return session(self::GUEST_NAME_FIELD, 'Guest');
    }

    public function setGuestName(string $name): void
    {
        session([self::GUEST_NAME_FIELD => $name]);
    }

    public function setOrGetGuestId()
    {
        $id = Str::ulid()->toString();
        if (! session()->has(self::GUEST_ID_FIELD)) {
            session([self::GUEST_ID_FIELD => $id]);
        }

        return session(self::GUEST_ID_FIELD);
    }

    public function setOrGetGuestName()
    {
        if (! session()->has(self::GUEST_NAME_FIELD)) {
            session([self::GUEST_NAME_FIELD => 'Guest']);
        }

        return session(self::GUEST_NAME_FIELD);
    }

    public function classHasTrait(object | string $classInstance, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($classInstance));
    }

    public function geDefaultUserAvatar(Authenticatable | Model | string $user)
    {
        if (is_string($user)) {
            $name = str($user)
                ->trim()
                ->explode(' ')
                ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
                ->join(' ');

            $backgroundColor = Rgb::fromString('rgb(' . FilamentColor::getColors()['gray'][950] . ')')->toHex();

            return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=' . str($backgroundColor)->after('#');
        } else {
            return app(UiAvatarsProvider::class)->get($user);
        }
    }
}
