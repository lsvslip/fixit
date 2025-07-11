<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bitrix_installations', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('auth_expires');
        });
    }

    public function down()
    {
        Schema::table('bitrix_installations', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }

};
