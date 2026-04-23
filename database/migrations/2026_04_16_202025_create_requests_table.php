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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->string('message');
            $table->decimal('price', 10, 2);
            $table->boolean('is_canceled')->default(false);
            $table->boolean('is_accepted')->nullable();
            $table->enum('status' , ['Nouveau' , 'En_Cour' , 'Terminer'])->default('Nouveau');
            $table->string('address');
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
