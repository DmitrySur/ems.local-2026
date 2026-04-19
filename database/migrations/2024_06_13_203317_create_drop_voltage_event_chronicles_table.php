<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drop_voltage_event_chronicles', function (Blueprint $table) {
            $table->comment('События хроники посадки');
            $table->id();
            $table->dateTime('datetime_event')->comment('Дата и время события');
            $table->text('description')->comment('Описание события');
            $table->foreignId('drop_voltage_id')->comment('Ссылка на посадку')->constrained('drop_voltages')->cascadeOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_chronicles');
    }
};
