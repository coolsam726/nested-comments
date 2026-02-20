#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

echo "🔍 Checking Nested Comments Package Readiness...\n\n";

$errors = [];

function check($label, $condition)
{
    global $errors;
    echo "- $label: ";
    if ($condition()) {
        echo "✅\n";
    } else {
        echo "❌\n";
        $errors[] = $label;
    }
}

check('PHPUnit passes', function () {
    $result = shell_exec('./vendor/bin/phpunit --filter test --stop-on-failure');

    return str_contains($result, 'OK') || str_contains($result, 'Tests:') && ! str_contains($result, 'ERROR');
});

check('Livewire class exists', fn () => class_exists(\Livewire\Livewire::class));

check('NestedCommentsServiceProvider is autoloadable', function () {
    return class_exists(\Hadialharbi\NestedComments\NestedCommentsServiceProvider::class);
});

check('comments.blade.php view exists', fn () => file_exists(__DIR__ . '/../resources/views/livewire/comments.blade.php'));

check('Lang files exist', fn () => is_dir(__DIR__ . '/../resources/lang/ar'));

check('Config file exists', fn () => file_exists(__DIR__ . '/../config/nested-comments.php'));

check('composer.json autoload is correct', function () {
    $composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);

    return isset($composer['autoload']['psr-4']['Hadialharbi\\NestedComments\\']);
});

check('Packagist name is updated', function () {
    $composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);

    return $composer['name'] === 'hadialharbi/nested-comments';
});

echo "\n";

if (empty($errors)) {
    echo "🎉 Package is READY to be tagged and pushed to Packagist.\n";
    exit(0);
} else {
    echo "⚠️ Please fix the following before publishing:\n";
    foreach ($errors as $e) {
        echo " - $e\n";
    }
    exit(1);
}
