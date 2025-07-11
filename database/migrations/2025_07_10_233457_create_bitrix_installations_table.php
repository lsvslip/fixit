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
        Schema::create('bitrix_installations', function (Blueprint $t) {
            $t->id();
            $t->string('domain')->unique();
            $t->string('app_sid')->unique();
            $t->string('auth_id');
            $t->string('refresh_id');
            $t->integer('auth_expires')->unsigned();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitrix_installations');
    }
};
