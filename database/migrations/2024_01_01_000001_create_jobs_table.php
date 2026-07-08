<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title');
            $table->string('company');
            $table->string('location')->nullable();
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->string('currency', 3)->nullable();
            $table->text('description');
            $table->json('skills')->nullable();
            $table->timestamp('posted_at');
            $table->string('source', 20)->default('internal');
            $table->string('external_id')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index('location');
            $table->index('posted_at');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
