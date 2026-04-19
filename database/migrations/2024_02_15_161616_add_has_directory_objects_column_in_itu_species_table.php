<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('itu_species', function (Blueprint $table) {
            $table->tinyInteger('has_directory_objects')
                ->default(0)
                ->comment('Флаг, что данный вид имеет значения в справочнике');
        });
    }

    public function down(): void
    {
        Schema::table('itu_species', function (Blueprint $table) {
            //
        });
    }
};
