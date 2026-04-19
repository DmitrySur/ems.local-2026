<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drop_voltage_devices', function (Blueprint $table) {
            $table->comment('Отключенные устройства по посадке');
            $table->id();
            $table->foreignId('drop_voltage_id')
                ->constrained('drop_voltages')
                ->cascadeOnDelete();
            $table->string('type')->comment('Тип устройства');
            $table->string('name', 500)->comment('Наименование')->nullable();
            $table->string('status')->comment('Статус');
            $table->string('comment', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drop_voltage_devices');
    }
};
