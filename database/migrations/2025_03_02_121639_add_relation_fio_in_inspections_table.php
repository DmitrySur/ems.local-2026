<?php

use App\Models\Inspection\Inspector;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->string('fio')
                ->nullable()
                ->change();
            $table->foreignIdFor(Inspector::class)
                ->nullable()
                ->comment('Ссылка на проверяющего')
                ->constrained('inspectors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            //
        });
    }
};
