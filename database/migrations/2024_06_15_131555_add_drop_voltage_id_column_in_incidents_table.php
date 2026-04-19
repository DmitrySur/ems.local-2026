<?php

use App\Models\DropVoltage\DropVoltage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignIdFor(DropVoltage::class)
                ->nullable()
                ->comment('Ссылка на посадку - причину инцидента')
                ->constrained('drop_voltages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            //
        });
    }
};
