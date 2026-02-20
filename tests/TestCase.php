<?php

namespace Hadialharbi\NestedComments\Tests;

use Hadialharbi\NestedComments\NestedCommentsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 👇 هذا هو المهم لتفعيل المايغريشن
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // لو عندك جدول users ضروري تنشئه هنا مؤقتًا:
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class, // ✅ أضف هذه السطر
            \Illuminate\Translation\TranslationServiceProvider::class,
            NestedCommentsServiceProvider::class,
            NestedSetServiceProvider::class, // أضف هذا السطر
        ];
    }

    protected function defineEnvironment($app)
    {

        $app['config']->set('app.locale', 'ar');
        $app['config']->set('app.fallback_locale', 'en');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // 👇 لو عندك config خاص بالحزمة
        $app['config']->set('nested-comments.tables.users', 'users');
    }
}
