<?php

return [

    'tables' => [
        'comments' => 'comments',
        'reactions' => 'reactions',
        'users' => 'users',
    ],

    'models' => [
        'comment' => Hadialharbi\NestedComments\Models\Comment::class,
        'reaction' => Hadialharbi\NestedComments\Models\Reaction::class,
        'user' => env('AUTH_MODEL', App\Models\User::class),
    ],

    'policies' => [
        'comment' => null,
        'reaction' => null,
    ],

    'allowed-reactions' => [
        '👍' => 'thumbs up',
        '👎' => 'thumbs down',
        '❤️' => 'heart',
        '😂' => 'laughing',
        '😮' => 'surprised',
        '😢' => 'crying',
        '💯' => 'hundred points',
        '🔥' => 'fire',
        '🎉' => 'party popper',
        '🚀' => 'rocket',
    ],

    'allow-all-reactions' => env('ALLOW_ALL_REACTIONS', false),
    'allow-multiple-reactions' => env('ALLOW_MULTIPLE_REACTIONS', false),
    'allow-guest-reactions' => env('ALLOW_GUEST_REACTIONS', false),
    'allow-guest-comments' => env('ALLOW_GUEST_COMMENTS', false),

    'format-created-date' => 'F j Y h:i:s A',

    'show-heading' => true,
    'show-badge-counter' => true,
    'badge-counter-color' => 'info',

    'show-refresh-button' => true,
    'style-refresh-button' => 'button',
    'icon-refresh-button' => 'heroicon-m-sparkles',
    'outlined-refresh-button' => false,
    'color-refresh-button' => 'info',

    // 🆕 إضافات مقترحة مستقبلًا:
    'enable_rtl' => true,

    'ui' => [
        'show_user_avatar' => true,
        'show_datetime_tooltip' => true,
    ],

    'permissions' => [
        'can_edit_comment' => true,
        'can_delete_comment' => true,
    ],
];
