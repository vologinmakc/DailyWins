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
        Schema::create('captchas', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('value');  // Значение капчи
            $table->timestamp('expires_at');  // Время истечения срока действия
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captchas');
    }
};
