<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            $table->time('start_time')
                ->nullable()
                ->comment('Дата начала проверки');
            $table->time('end_time')
                ->nullable()
                ->comment('Дата окончания проверки');
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $table) {
            //
        });
    }
};
