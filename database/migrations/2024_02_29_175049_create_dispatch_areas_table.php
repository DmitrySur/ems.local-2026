<?php

use App\Models\Directories\GroupInfrastructureObject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dispatch_areas', function (Blueprint $table) {
            $table->comment('Диспетчерские участки');
            $table->id();
            $table->string('name')->comment('Наименование');
            $table->foreignIdFor(GroupInfrastructureObject::class)
                ->comment('Ссылка на группу объектов инфраструктуры')
                ->nullable()
                ->constrained('group_infrastructure_objects')
                ->restrictOnDelete();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_areas');
    }
};
