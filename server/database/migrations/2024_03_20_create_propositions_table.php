<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('propositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demandes')->onDelete('cascade');
            $table->foreignId('donneur_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('heure');
            $table->text('message')->nullable();
            $table->enum('status', ['en_attente', 'approuve', 'rejete'])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('propositions');
    }
}; 