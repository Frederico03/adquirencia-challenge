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
        Schema::create('pix_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subadquirente_id')->constrained('subadquirentes')->onDelete('cascade');
            $table->string('external_id');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['PENDING', 'CONFIRMED', 'FAILED'])->default('PENDING');
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pix_transactions');
    }
};
