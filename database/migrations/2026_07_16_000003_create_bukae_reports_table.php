<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukae_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->index();
            $table->string('device_id', 64);
            $table->timestamp('created_at')->useCurrent();
            // 기기당 부캐당 1회 신고
            $table->unique(['post_id', 'device_id'], 'uniq_report');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukae_reports');
    }
};
