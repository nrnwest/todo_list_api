<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('parent_id')->nullable();
            $table->string('status')->default('todo');
            $table->tinyInteger('priority')->unsigned();
            $table->string('title');
            $table->text('description');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->fullText(['title', 'description']);
            $table->index(['user_id', 'parent_id']);
            $table->index(['created_at', 'completed_at']);
            $table->index(['priority', 'status']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
