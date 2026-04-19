<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_chronicles', function (Blueprint $table) {
            $table->comment('События хроники инцидентов');
            $table->id();
            $table->dateTime('datetime_event')->comment('Дата и время события');
            $table->text('description')->comment('Описание события');
            $table->string('is_show_in_reports')->default(true)->comment('Флаг отражения события в сводке');
            $table->foreignId('incident_id')->comment('Ссылка на инцидент')->constrained('incidents')->cascadeOnDelete();
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
