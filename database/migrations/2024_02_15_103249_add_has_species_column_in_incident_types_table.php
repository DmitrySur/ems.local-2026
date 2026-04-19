<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incident_types', function (Blueprint $table) {
            $table->tinyInteger('has_species')->default(false)->comment('Флаг, что данный тип имеет виды ИТУ');
        });
    }

    public function down(): void
    {
        Schema::table('incident_types', function (Blueprint $table) {
            //
        });
    }
};
