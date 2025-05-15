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
        Schema::create('reponse_besoins', function (Blueprint $table) {
            $table->id();
            $table->enum('etat', ['pending', 'accepted', 'rejected']);
            $table->text('message')->nullable();
            $table->timestamp('date_reponse');
            $table->boolean('valide')->default(false);
            $table->foreignId('demande_id')->constrained('demandes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reponse_besoins');
    }
};
