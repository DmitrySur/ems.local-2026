<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('itu_directory_objects', function (Blueprint $table) {
            $table->comment('Справочник ИТУ');
            $table->id();
            $table->string('title', 400)->comment('Наименование');
            $table->foreignId('incident_type_id')
                ->comment('Ссылка на тип инцидента')
                ->constrained('incident_types')
                ->cascadeOnDelete();
            $table->foreignId('itu_specie_id')
                ->comment('Ссылка на вид ИДУ')
                ->nullable()
                ->constrained('itu_species')
                ->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itu_directory_objects');
    }
};
