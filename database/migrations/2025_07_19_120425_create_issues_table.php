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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('issue_type'); // object, process, employee
            $table->string('object_name');
            $table->text('issue_description');
            $table->text('expectations_description');
            $table->string('bitrix_task_id')->nullable(); // ID задачи в Bitrix24
            $table->string('status')->default('new'); // new, in_progress, completed, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
