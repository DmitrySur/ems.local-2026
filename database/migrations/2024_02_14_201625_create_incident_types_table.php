<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incident_types', function (Blueprint $table) {
            $table->comment('Таблица типов инцидентов');
            $table->id();
            $table->string('title', 400)->comment('Наименование типа инцидента');
            $table->tinyInteger('has_characteristic')->comment('Флаг, что данный тип имеет характеристики')->default(false);
            $table->tinyInteger('has_elements')->comment('Флаг, что данный тип имеет элементы')->default(false);
            $table->tinyInteger('has_faults')->comment('Флаг, что данный тип имеет неисправности')->default(false);
            $table->tinyInteger('has_directory_objects')->comment('Флаг, что данный тип имеет справочник ИТУ')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_types');
    }
};
