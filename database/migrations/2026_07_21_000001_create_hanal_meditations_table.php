<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hanal_meditations', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();       // 오늘의 말씀 = 날짜당 하나
            $table->string('verse_ref', 64);       // "시편 46:10-11" (범위 가능)
            $table->text('verse_text');            // DB에서 주입한 개역한글 원문
            $table->string('title', 40);
            $table->string('hashtag', 40);
            $table->string('context_line', 120);
            $table->json('body');                  // 3~4단락
            $table->text('prayer');
            $table->json('tags');
            // 관측용 메타 (판정 근거)
            $table->string('source', 24);          // generated|regenerated|archive_fallback
            $table->string('structure', 16)->nullable();
            $table->string('opener', 16)->nullable();
            $table->string('closer', 16)->nullable();
            $table->json('theme_tags')->nullable(); // 그날 선곡 정서 태그
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hanal_meditations');
    }
};