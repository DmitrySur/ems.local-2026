<?php

use App\Models\Directories\ObjectInfrastructure;
use App\Models\Inspection\Inspection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inf_objs_inspections_pivot', function (Blueprint $table) {
            $table->comment('Сводная таблица проверок и местонахождений');
            $table->id();
            $table->foreignIdFor(ObjectInfrastructure::class)
                ->constrained('object_infrastructures')
                ->cascadeOnDelete();
            $table->foreignIdFor(Inspection::class)
                ->constrained('inspections')
                ->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inf_objs_inspections_pivot');
    }
};
