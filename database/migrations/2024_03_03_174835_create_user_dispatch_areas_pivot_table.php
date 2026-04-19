<?php

use App\Models\Directories\DispatchArea;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_dispatch_areas_pivot', function (Blueprint $table) {
            $table->comment('Сводная таблица диспетчерских участков и пользователей');
            $table->id();
            $table->foreignIdFor(User::class)
                ->comment('Ссылка на пользователя')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignIdFor(DispatchArea::class)
                ->comment('Ссылка на диспетчерский участок')
                ->nullable()
                ->constrained('dispatch_areas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dispatch_areas_pivot');
    }
};
