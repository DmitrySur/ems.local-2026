<?php

use App\Models\Directories\GroupInfrastructureObject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->comment('Подразделение');
            $table->id();
            $table->string('name', 800)->comment('Полное наименование')->nullable();
            $table->string('short_name', 100)->comment('Краткое наименование')->nullable();
            $table->boolean('has_group_object')->default(false)->comment('Флаг привязки подразделения к группам объектов инфраструктуры');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreignIdFor(GroupInfrastructureObject::class)
                ->comment('Ссылка на группу объектов инфраструктуры при наличии')
                ->nullable()
                ->constrained('group_infrastructure_objects')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
