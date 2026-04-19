<?php

use App\Models\Directories\DispatchArea;
use App\Models\Directories\GroupInfrastructureObject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disp_ar_gr_infr_obj_pivot', function (Blueprint $table) {
            $table->comment('Сводная таблица диспетчерских участков и групп объектов инфраструктуры');
            $table->id();
            $table->foreignIdFor(GroupInfrastructureObject::class)
                ->comment('Ссылка на группу объектов инфраструктуры')
                ->nullable()
                ->constrained('group_infrastructure_objects')
                ->restrictOnDelete();
            $table->foreignIdFor(DispatchArea::class)
                ->comment('Ссылка на диспетчерский участок')
                ->nullable()
                ->constrained('dispatch_areas')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_areas_group_infrastructure_objects_pivot');
    }
};
