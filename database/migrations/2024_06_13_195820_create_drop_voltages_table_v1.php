<?php

use App\Models\Directories\GroupInfrastructureObject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('drop_voltages', function (Blueprint $table) {
            $table->comment('Основная таблица с посадками');
            $table->id();
            $table->dateTime('datetime_drop')->comment('Дата и время посадки');
            $table->foreignIdFor(GroupInfrastructureObject::class)
                ->constrained('group_infrastructure_objects')
                ->restrictOnDelete();
            $table->string('detail_location', 800)
                ->comment('Уточнение местоположения')
                ->nullable();
            $table->string('detail_drop', 800)
                ->nullable()
                ->comment('Уточнение по посадке');
            $table->string('status_drop')
                ->nullable()
                ->comment('Статус устранения инцидента');
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drop_voltages');
    }
};
