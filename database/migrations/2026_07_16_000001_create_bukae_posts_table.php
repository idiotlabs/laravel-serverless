<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukae_posts', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 64)->index();
            $table->unsignedInteger('fc'); // faceColor ARGB
            $table->unsignedTinyInteger('fs');
            $table->unsignedTinyInteger('e');
            $table->unsignedTinyInteger('m');
            $table->unsignedTinyInteger('h');
            $table->string('archetype', 64);
            $table->string('tagline', 160);
            $table->boolean('hidden')->default(false)->index();
            $table->unsignedInteger('report_count')->default(0);
            $table->timestamp('created_at')->useCurrent()->index();
            // 캐릭터당 1회 공유(같은 기기+같은 조합 중복 방지)
            $table->unique(['device_id', 'fc', 'fs', 'e', 'm', 'h'], 'uniq_device_combo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukae_posts');
    }
};
