<?php

use App\Models\Directories\DispatchArea;
use App\Models\Directories\Division;
use App\Models\Directories\IncidentType;
use App\Models\Directories\ItuCharacteristic;
use App\Models\Directories\ItuDirectoryObject;
use App\Models\Directories\ItuElement;
use App\Models\Directories\ItuFault;
use App\Models\Directories\ItuReasonBreakdown;
use App\Models\Directories\ItuSpecie;
use App\Models\Directories\ObjectInfrastructure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->comment('Основная таблица с инцидентами');
            $table->dateTime('datetime_incident')->comment('Дата и время инцидента');
            $table->foreignIdFor(ObjectInfrastructure::class)
                ->comment('Ссылка на объект инфраструктуры')
                ->constrained('object_infrastructures')
                ->restrictOnDelete();
            $table->string('location')
                ->nullable()
                ->comment('Местоположение');
            $table->string('detail_location', 800)
                ->comment('Уточнение местоположения')
                ->nullable();
            $table->string('reported_by', 600)
                ->comment('Сообщил')
                ->nullable();
            $table->foreignIdFor(Division::class)
                ->comment('Ссылка на подразделение')
                ->nullable()
                ->constrained('divisions')
                ->restrictOnDelete();
            $table->foreignIdFor(IncidentType::class)
                ->comment('Ссылка на тип инцидента')
                ->nullable()
                ->constrained('incident_types')
                ->restrictOnDelete();
            $table->foreignIdFor(ItuSpecie::class)
                ->comment('Ссылка на вид инцидента')
                ->nullable()
                ->constrained('itu_species')
                ->restrictOnDelete();
            $table->foreignIdFor(ItuCharacteristic::class)
                ->comment('Ссылка на характеристику ИТУ')
                ->nullable()
                ->constrained('itu_characteristics')
                ->restrictOnDelete();
            $table->foreignIdFor(ItuDirectoryObject::class)
                ->comment('Ссылка на характеристику ИТУ')
                ->nullable()
                ->constrained('itu_directory_objects')
                ->restrictOnDelete();
            $table->foreignIdFor(ItuFault::class)
                ->comment('Ссылка на неисправность')
                ->nullable()
                ->constrained('itu_faults')
                ->restrictOnDelete();
            $table->foreignIdFor(ItuElement::class)
                ->comment('Ссылка на элемент')
                ->nullable()
                ->constrained('itu_elements')
                ->restrictOnDelete();
            $table->foreignIdFor(ItuReasonBreakdown::class)
                ->comment('Ссылка на элемент')
                ->nullable()
                ->constrained('itu_reason_breakdowns')
                ->restrictOnDelete();
            $table->string('detail_object_incident', 450)
                ->nullable()
                ->comment('Уточнение по объекту инцидента');
            $table->string('detail_incident', 450)
                ->nullable()
                ->comment('Уточнение по инциденту/неисправности');
            $table->string('incident_classification')
                ->nullable()
                ->comment('Тип инцидента (ННР/ТО и т.д.)');
            $table->string('number_nnr')
                ->nullable()
                ->comment('Номер ННР при наличии');
            $table->text('appropriate_measures')
                ->nullable()
                ->comment('Принятые меры');
            $table->text('note')
                ->nullable()
                ->comment('Примечание');
            $table->string('status_resolution')
                ->nullable()
                ->comment('Статус устранения инцидента');
            $table->string('status_incident')
                ->nullable()
                ->comment('Статус устранения инцидента');
            $table->foreignIdFor(DispatchArea::class)
                ->comment('Ссылка на курирующий участок ДП')
                ->nullable()
                ->constrained('dispatch_areas')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
