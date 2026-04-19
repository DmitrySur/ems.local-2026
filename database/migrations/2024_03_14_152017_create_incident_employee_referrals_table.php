<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incident_employee_referrals', function (Blueprint $table) {
            $table->comment('Информация о направлении работников');
            $table->id();
            $table->string('position')->comment('Должность работника');
            $table->string('fio')->comment('ФИО работника');
            $table->time('direction_time')->comment('Время направления');
            $table->time('arrival_time')->nullable()->comment('Время прибытия');
            $table->foreignId('incident_type_id')->comment('Ссылка на инцидент')
            ->constrained('incidents')->cascadeOnDelete();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_employee_referrals');
    }
};
