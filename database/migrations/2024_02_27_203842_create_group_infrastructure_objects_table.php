<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_infrastructure_objects', function (Blueprint $table) {
            $table->comment('Группы объектов инфраструктуры');
            $table->id();
            $table->string('title', 600)->comment('Наименование группы объектов инфраструктуры');
            $table->string('short_title')->comment('Краткое наименование группы объектов инфраструктуры');;
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_infrastructure_objects');
    }
};
