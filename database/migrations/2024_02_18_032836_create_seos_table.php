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
        Schema::create('seos', function (Blueprint $table) {
            $table->id();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('meta_robots', 255)->nullable();
            $table->string('meta_author', 255)->nullable();
            $table->string('meta_canonical', 255)->nullable();
            $table->string('meta_og_title', 255)->nullable();
            $table->string('meta_og_description', 255)->nullable();
            $table->string('meta_og_image', 255)->nullable();
            $table->string('meta_og_type', 255)->nullable();
            $table->string('meta_og_url', 255)->nullable();
            $table->string('meta_og_locale', 255)->nullable();
            // $table->string('meta_twitter_title', 255)->nullable();
            // $table->string('meta_twitter_description', 255)->nullable();
            // $table->string('meta_twitter_image', 255)->nullable();
            // $table->string('meta_twitter_card', 255)->nullable();
            // $table->string('meta_twitter_site', 255)->nullable();
            // $table->string('meta_twitter_creator', 255)->nullable();
            // $table->string('meta_twitter_url', 255)->nullable();
            // $table->string('meta_twitter_locale', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seos');
    }
};
