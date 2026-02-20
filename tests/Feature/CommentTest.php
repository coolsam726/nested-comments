<?php

namespace Hadialharbi\NestedComments\Tests\Feature;

use Hadialharbi\NestedComments\Models\Comment;
use Hadialharbi\NestedComments\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء موديل Post وهمي إذا لم يكن موجوداً
        if (! class_exists(\App\Models\Post::class)) {
            eval('
                namespace App\Models;

                use Illuminate\Database\Eloquent\Model;

                class Post extends Model {
                    protected $guarded = [];
                    public $timestamps = false;
                }
            ');
            Schema::create('posts', function ($table) {
                $table->id();
            });
        }

        \App\Models\Post::create(); // منشور وهمي
    }

    /** @test */
    public function it_can_create_a_comment()
    {
        // إنشاء تعليق
        $comment = Comment::create([
            'commentable_type' => \App\Models\Post::class,
            'commentable_id' => 1,
            'body' => 'هذا تعليق تجريبي',
            'user_id' => 1,
        ]);

        // التحقق من وجود التعليق في قاعدة البيانات
        $this->assertDatabaseHas('comments', [
            'body' => 'هذا تعليق تجريبي',
            'user_id' => 1,
            'commentable_type' => \App\Models\Post::class,
            'commentable_id' => 1,
        ]);
    }
}
