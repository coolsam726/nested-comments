<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->nestedSet();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->morphs('commentable');
            $table->ulid('guest_id')->nullable()->index();
            $table->string('guest_name')->nullable();
            $table->ipAddress()->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->morphs('reactable');
            $table->string('emoji');
            $table->ulid('guest_id')->nullable()->index();
            $table->string('guest_name')->nullable();
            $table->ipAddress()->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('comments');
    }
};
