<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukae_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->index();
            $table->string('device_id', 64)->index();
            $table->string('emoji', 16);
            $table->timestamp('created_at')->useCurrent();
            // 여러 이모지 OK, 같은 이모지는 기기당 부캐당 1개(토글)
            $table->unique(['post_id', 'device_id', 'emoji'], 'uniq_reaction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukae_reactions');
    }
};
