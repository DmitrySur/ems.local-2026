<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->comment('Таблица учета проверок (ночные, день. без. и т.д.');
            $table->id();
            $table->string('position')->comment('Должность проверяющего');
            $table->string('fio')->comment('ФИО проверяющего');
            $table->string('type')->comment('Тип проверки');
            $table->date('date_start')->comment('Дата начала проверки');
            $table->foreignId('division_id')
                ->comment('Ссылка на подразделение проверяющего')
                ->constrained('divisions')
                ->restrictOnDelete();
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
