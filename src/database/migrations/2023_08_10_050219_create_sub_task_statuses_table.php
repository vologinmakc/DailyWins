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
        Schema::create('sub_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_task_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('status');
            $table->string('commentary', '512')->nullable();
            $table->timestamps();

            $table->unique(['sub_task_id', 'date']); // Уникальное сочетание подзадачи и даты
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_task_statuses');
    }
};
