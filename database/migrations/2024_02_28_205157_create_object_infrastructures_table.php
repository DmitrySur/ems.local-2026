<?php

use App\Models\Directories\GroupInfrastructureObject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('object_infrastructures', function (Blueprint $table) {
            $table->comment('Объекты инфраструктуры');
            $table->id();
            $table->string('name', 800)->nullable()->comment('Наименование объекта');
            $table->string('type')->nullable()->comment('Тип объекта');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreignIdFor(GroupInfrastructureObject::class)
                ->comment('Ссылка на группу обьекта инфраструктуры')
                ->constrained('group_infrastructure_objects')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('object_infrastructures');
    }
};
