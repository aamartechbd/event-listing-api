<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->enum('event_type', ['online', 'physical', 'hybrid']);
            $table->string('country');
            $table->string('venue');
            $table->date('event_date');
            $table->time('event_time');
            $table->string('category');
            $table->string('website_link');
            $table->text('description');
            $table->string('video_link')->nullable();
            $table->string('featured_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
