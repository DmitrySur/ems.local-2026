<?php

use App\Models\Incident\Incident;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incident_employee_information', function (Blueprint $table) {
            $table->comment('Оповещение работников об инциденте');
            $table->id();
            $table->string('position')->comment('Должность работника');
            $table->string('fio')->comment('ФИО работника');
            $table->time('information_time')->comment('Время оповещения');
            $table->foreignIdFor(Incident::class)->comment('Ссылка на инцидент')
                ->constrained('incidents')->cascadeOnDelete();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_employee_information');
    }
};
