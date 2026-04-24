<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('Nouveau', 'En_Cour', 'Terminer', 'Refuser') DEFAULT 'Nouveau'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('Nouveau', 'En_Cour', 'Terminer') DEFAULT 'Nouveau'");
    }
};
