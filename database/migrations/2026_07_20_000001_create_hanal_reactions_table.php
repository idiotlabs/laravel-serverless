<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hanal_reactions', function (Blueprint $table) {
            $table->id();
            // 말씀 ID = 장절 ref ("시편 46:10-11"). 오늘의 말씀은 모두에게 하나이므로
            // 날짜가 아니라 말씀 ref 기준으로 집계한다(지난 말씀 반응도 같은 키로 누적).
            $table->string('meditation_id', 64)->index();
            $table->string('device_id', 64)->index();
            $table->string('reaction', 16); // grace|thanks|pierced|peace|joy
            $table->timestamp('created_at')->useCurrent();
            // 여러 반응 OK, 같은 반응은 기기당 말씀당 1개(토글)
            $table->unique(['meditation_id', 'device_id', 'reaction'], 'uniq_hanal_reaction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hanal_reactions');
    }
};
