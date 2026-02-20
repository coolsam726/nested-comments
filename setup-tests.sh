#!/bin/bash

echo "🚀 إنشاء ملفات الاختبارات لحزمة NestedComments..."

BASE_DIR=$(pwd)

# إنشاء مجلدات
mkdir -p "$BASE_DIR/tests/Feature"

# ملف TestCase.php
cat > "$BASE_DIR/tests/TestCase.php" << 'EOF'
<?php

namespace Hadialharbi\NestedComments\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Hadialharbi\NestedComments\NestedCommentsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            NestedCommentsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
EOF

# ملف CommentTest.php
cat > "$BASE_DIR/tests/Feature/CommentTest.php" << 'EOF'
<?php

namespace Hadialharbi\NestedComments\Tests\Feature;

use Hadialharbi\NestedComments\Models\Comment;
use Hadialharbi\NestedComments\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_comment()
    {
        $comment = Comment::create([
            'commentable_type' => 'App\\Models\\Post',
            'commentable_id' => 1,
            'user_id' => 1,
            'content' => 'تعليق تجريبي',
        ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'تعليق تجريبي',
        ]);
    }
}
EOF

# ملف phpunit.xml
cat > "$BASE_DIR/phpunit.xml" << 'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php"
         colors="true"
         verbose="true">
    <testsuites>
        <testsuite name="Package Test Suite">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
EOF

echo "✅ تم إنشاء ملفات الاختبارات بنجاح!"
