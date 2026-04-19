<?php

namespace App\Filament\Resources\Incident;

use App\Enum\IncidentStatuses;
use App\Enum\TypesInfrastructureObjectEnum;
use App\Filament\Resources\Incident\IncidentResource\Pages;
use App\Models\Directories\IncidentType;
use App\Models\Directories\ItuDirectoryObject;
use App\Models\Directories\ItuSpecie;
use App\Models\Directories\ObjectInfrastructure;
use App\Models\Incident\EventChronicles;
use App\Models\Incident\Incident;
use Auth;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;
    protected static ?string $label = 'Инцидент';
    protected static ?string $pluralLabel = 'Инциденты';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $incidentTypesList = IncidentType::get();
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Основные данные')->schema([
                            Forms\Components\Grid::make(5)
                                ->schema([
                                    //Дата и время инцидента
                                    Forms\Components\DateTimePicker::make('datetime_incident')
                                        ->label('Дата и время')
                                        ->seconds(false)
                                        ->native(false)
                                        ->displayFormat('d.m.Y H:i')
                                        ->default(now())
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->closeOnDateSelection(),

                                    //Объект инфраструктуры
                                    Forms\Components\Select::make('object_infrastructure_id')
                                        ->label('Объект')
                                        ->relationship('objectInfrastructure', 'name', function (Builder $query) {
                                            if (Auth::user()->hasRole('linear_dispatcher')) {
                                                $groupInfrastructureObjectsList = Auth::user()->dispatchArea->groupInfrastructureObjects()->get()->pluck('id')->toArray();
                                                if ($groupInfrastructureObjectsList && is_array($groupInfrastructureObjectsList) && count($groupInfrastructureObjectsList) > 0) {
                                                    return $query->whereIn('group_infrastructure_object_id',
                                                        $groupInfrastructureObjectsList)
                                                        ->with('groupInfrastructureObject');
                                                }
                                            }
                                            return $query->with('groupInfrastructureObject');
                                        })
                                        ->columnSpan(2)
                                        ->getOptionLabelFromRecordUsing(function (ObjectInfrastructure $record) {
                                            return TypesInfrastructureObjectEnum::tryFrom($record->type)->getLabel() .
                                                ' ' .
                                                $record->name .
                                                ' (' .
                                                $record->groupInfrastructureObject->short_title .
                                                ')';
                                        })
                                        ->searchable(['name'])
                                        ->preload()
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->exists('object_infrastructures', 'id')
                                        ->required()
                                        ->afterStateUpdated(function (Forms\Set $set, ?string $state, ?string $old) {
                                            if ($old !== $state) {
                                                $set('subdivision', null);
                                                $set('dispatch_area_id', ObjectInfrastructure::firstWhere('id', $state)
                                                    ?->groupInfrastructureObject?->dispatchAreas()?->first()?->id ?? null);
                                            }
                                        })
                                        ->live()
                                    ,

                                    //Расположение (путь, БТП)
                                    Forms\Components\Select::make('location')
                                        ->label('Местоположение')
                                        ->options(function () {
                                            return array_combine(config('incident_locations'),
                                                config('incident_locations'));
                                        })
                                        ->searchable()
                                        ->required()
                                        ->in(config('incident_locations'))
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel()),

                                    //Подразделение
                                    Forms\Components\Select::make('division_id')
                                        ->label('Подразделение')
                                        ->relationship('division', 'short_name',
                                            function (Builder $query, Forms\Get $get) {
                                                if ($get('object_infrastructure_id')) {
                                                    $objectInfrastructureGroupId = ObjectInfrastructure::where('id',
                                                        $get('object_infrastructure_id'))->first()?->groupInfrastructureObject?->id;
                                                    if ($objectInfrastructureGroupId) {
                                                        return $query->whereHas('groupInfrastructureObject',
                                                            function (Builder $query) use ($objectInfrastructureGroupId
                                                            ) {
                                                                return $query->where('id',
                                                                    $objectInfrastructureGroupId);
                                                            })->orWhere('has_group_object', false);
                                                    }
                                                }
                                                return $query;
                                            })
                                        ->preload()
                                        ->searchable()
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->exists('divisions', 'id'),

                                    //Уточнение местоположения
                                    Forms\Components\TextInput::make('detail_location')
                                        ->label('Уточнение местоположения')
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->maxLength(400)
                                        ->columnSpan(3)
                                        ->hint('Пикет/№ помещения'),

                                    //Поле "сообщил"
                                    Forms\Components\TextInput::make('reported_by')
                                        ->label('Сообщил')
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->columnSpan(2)
                                        ->live()
                                        ->hint(function (Component $component, Get $get) {
                                            $listArray = IncidentType::firstWhere('id',
                                                $get('incident_type_id'))?->reported_by_list ?? [];
                                            if (count($listArray) > 0) {
                                                return view('filament.forms.incident.reported_by_action_dropdown',
                                                    ['my_component' => $component, 'my_list' => $listArray]);
                                            }
                                            return '';
                                        }),
                                ]),
                            Forms\Components\Grid::make(4)
                                ->schema([
                                    //Тип инцидента
                                    Forms\Components\Select::make('incident_type_id')
                                        ->label('Тип инцидиента (ИТУ)')
                                        ->relationship('incidentType', 'title')
                                        ->preload()
                                        ->searchable()
                                        ->live()
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->exists('incident_types', 'id')
                                        ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                                            if ($old !== $state) {
                                                $set('itu_specie_id', null);
                                                $set('itu_characteristic_id', null);
                                                $set('itu_directory_object_id', null);
                                                $set('itu_fault_id', null);
                                                $set('itu_element_id', null);
                                                $set('itu_reason_breakdown_id', null);
                                            }
                                        }),

                                    //Вид инцидента (ИТУ)
                                    Forms\Components\Select::make('itu_specie_id')
                                        ->label('Вид инцидента (ИТУ)')
                                        ->relationship('ituSpecie', 'title', function (Builder $query, Get $get) {
                                            return $query->where('incident_type_id', $get('incident_type_id'));
                                        })
                                        ->preload()
                                        ->searchable()
                                        ->visible(function (Get $get) use ($incidentTypesList) {
                                            return (bool)$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_species ?? false;
                                        })
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->exists('itu_species', 'id')
                                        ->live(),

                                    //Характеристика ИТУ
                                    Forms\Components\Select::make('itu_characteristic_id')
                                        ->label('Характеристики ИТУ')
                                        ->searchable()
                                        ->relationship('ituCharacteristic', 'title',
                                            function (Builder $query, Get $get) {
                                                return $query->where('incident_type_id', $get('incident_type_id'));
                                            })
                                        ->preload()
                                        ->visible(function (Get $get) use ($incidentTypesList) {
                                            return (bool)$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_characteristic ?? false;
                                        })
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->exists('itu_characteristics', 'id'),

                                    //Объект ИТУ при наличии
                                    Forms\Components\Select::make('itu_directory_object_id')
                                        ->label('Объект ИТУ')
                                        ->preload()
                                        ->searchable()
                                        ->relationship('ituDirectoryObject', 'title',
                                            function (Builder $query, Get $get) {
                                                $incidentTypeId = $get('incident_type_id');
                                                $ituSpecieId = $get('itu_specie_id');
                                                return $query->with([
                                                    'ituSpecie' => function (BelongsTo $query) {
                                                        return $query->select('id', 'title');
                                                    }
                                                ])
                                                    ->when($incidentTypeId, function (Builder $query, $incidentTypeId) {
                                                        $query->where('incident_type_id', $incidentTypeId);
                                                    })
                                                    ->when($ituSpecieId, function (Builder $query, $ituSpecieId) {
                                                        $query->where('itu_specie_id', $ituSpecieId);
                                                    });
                                            })
                                        ->getOptionLabelFromRecordUsing(function (ItuDirectoryObject $record) {
                                            return $record->ituSpecie->title . '-' . $record->title;
                                        })
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->exists('itu_directory_objects', 'id')
                                        ->createOptionForm(function (Get $get) {
                                            return [
                                                Cluster::make([
                                                    Hidden::make('incident_type_id')
                                                        ->required()
                                                        ->default($get('incident_type_id')),
                                                    Select::make('itu_specie_id')
                                                        ->label('Вид ИТУ')
                                                        ->searchable()
                                                        ->exists('itu_species', 'id')
                                                        ->live()
                                                        ->options(function (Get $get) {
                                                            return ItuSpecie::where([
                                                                'incident_type_id' => $get('incident_type_id'),
                                                                'has_directory_objects' => true
                                                            ])
                                                                ->get()
                                                                ->pluck('title', 'id')
                                                                ->toArray();
                                                        })
                                                        ->default($get('itu_specie_id'))
                                                        ->disabled()
                                                        ->dehydrated(),
                                                    TextInput::make('title')
                                                        ->prefix('№')
                                                        ->label('Номер')
                                                        ->validationAttribute(fn(Component $component
                                                        ): string => $component->getLabel())
                                                        ->required()
                                                        ->maxLength(150)
                                                        ->dehydrateStateUsing(fn(string $state
                                                        ): string => str_replace(' ',
                                                            '', $state))
                                                        ->unique(ignoreRecord: true, modifyRuleUsing: function (
                                                            Unique    $rule,
                                                            Forms\Get $get
                                                        ) {
                                                            return $rule
                                                                ->where('incident_type_id', $get('incident_type_id'))
                                                                ->where('itu_specie_id', $get('itu_specie_id'));
                                                        })
                                                ])
                                                    ->label('Вид и номер ИТУ')
                                            ];
                                        })
                                        ->visible(function (Get $get) {
                                            return ItuSpecie::firstWhere('id',
                                                $get('itu_specie_id'))?->has_directory_objects ?? false;
                                        }),
                                ]),

                            Forms\Components\Grid::make(3)
                                ->schema([
                                    //Неисправность
                                    Forms\Components\Select::make('itu_fault_id')
                                        ->label('Неисправность')
                                        ->relationship('ituFault', 'title', function (Builder $query, Get $get) {
                                            return $query->where('incident_type_id', $get('incident_type_id'));
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->visible(function (Get $get) use ($incidentTypesList) {
                                            return (bool)$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_faults ?? false;
                                        })
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->requiredIf('status_resolution', IncidentStatuses::InWorking->value)
                                        ->exists('itu_faults', 'id')
                                        ->validationMessages([
                                            'required_if' => 'Поле :attribute обязательно для заполнения',
                                        ]),

                                    //Элемент ИТУ
                                    Forms\Components\Select::make('itu_element_id')
                                        ->label('Неисправный элемент')
                                        ->relationship('ituElement', 'title', function (Builder $query, Get $get) {
                                            return $query->where('incident_type_id', $get('incident_type_id'));
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->visible(function (Get $get) use ($incidentTypesList) {
                                            return (bool)$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_elements ?? false;
                                        })
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->requiredIf('status_resolution', IncidentStatuses::InWorking->value)
                                        ->exists('itu_elements', 'id')
                                        ->validationMessages([
                                            'required_if' => 'Поле :attribute обязательно для заполнения',
                                        ]),

                                    //Причина неисправности
                                    Forms\Components\Select::make('itu_reason_breakdown_id')
                                        ->label('Причина')
                                        ->relationship('ituReasonBreakdown', 'title',
                                            function (Builder $query, Get $get) {
                                                return $query->where('incident_type_id', $get('incident_type_id'));
                                            })
                                        ->searchable()
                                        ->preload()
                                        ->visible(function (Get $get) use ($incidentTypesList) {
                                            return (bool)$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_faults ?? false;
                                        })
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->requiredIf('status_resolution', IncidentStatuses::InWorking->value)
                                        ->exists('itu_reason_breakdowns', 'id')
                                        ->validationMessages([
                                            'required_if' => 'Поле :attribute обязательно для заполнения',
                                        ]),
                                ]),
                            Forms\Components\Grid::make()
                                ->schema([
                                    //Уточнение по объекту инцидента
                                    Forms\Components\TextInput::make('detail_object_incident')
                                        ->label('Уточнение по объекту инцидента')
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->maxLength(225),

                                    //Описание ициндента/неисправности
                                    Forms\Components\TextInput::make('detail_incident')
                                        ->label('Описание инцидента/неисправности')
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->maxLength(225)
                                        ->required(function (Get $get) use ($incidentTypesList) {
                                            return !$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_faults ?? false;
                                        })
                                        ->extraInputAttributes(function (Get $get) use ($incidentTypesList) {
                                            if (!$incidentTypesList->firstWhere('id', '=',
                                                $get('incident_type_id'))?->has_faults ?? false) {
                                                return ['style' => 'background-color: #FFFFCC'];
                                            }
                                            return [];
                                        }),
                                ]),
                            Forms\Components\Grid::make(4)
                                ->schema([
                                    Cluster::make([

                                        //Вид инцидента (ННР, ТО и т.д.)
                                        Forms\Components\Select::make('incident_classification')
                                            ->label('Вид инцидента')
                                            ->placeholder('Выбрать')
                                            ->searchable()
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->options([
                                                'ННР' => 'ННР',
                                                'ТО' => 'ТО',
                                                'АРМ' => 'АРМ'
                                            ])
                                            ->in(fn(Select $component): array => $component->getOptions() ?? [])
                                            ->required()
                                            ->live(),

                                        //Номер инцидента при наличии
                                        Forms\Components\TextInput::make('number_nnr')
                                            ->label('Номер ННР')
                                            ->dehydrateStateUsing(fn(?string $state): string => str_replace(' ', '',
                                                $state))
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->maxLength(125)
                                    ])
                                        ->label('Тип и номер инцидента'),

                                    //Принятые меры
                                    Forms\Components\Textarea::make('appropriate_measures')
                                        ->label('Принятые меры')
                                        ->rows(2)
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->maxLength(1500)
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required(function (Get $get) {
                                            $status = $get('status_resolution')?->value ?? '';
                                            return $status === IncidentStatuses::InWorking->value;
                                        }),

                                    //Статус устранения
                                    Forms\Components\Select::make('status_resolution')
                                        ->label('Статус инцидента')
                                        ->searchable()
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->required()
                                        ->default(IncidentStatuses::InRepair->value)
                                        ->enum(IncidentStatuses::class)
                                        ->options(IncidentStatuses::class)
                                        ->live(),

                                    //Дата устранения, закрытия инцидента
                                    Forms\Components\DateTimePicker::make('repair_date')
                                        ->label('Дата закрытия инцидента')
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->native(false)
                                        ->default(now())
                                        ->displayFormat('d.m.Y H:i')
                                        ->visible(function (Get $get) {
                                            return $get('status_resolution') === IncidentStatuses::InWorking->value;
                                        })
                                        ->required()
                                        ->afterOrEqual('datetime_incident'),
                                ]),
                        ]),

                        //Направление работника
                        Forms\Components\Tabs\Tab::make('Направление работников')
                            ->schema([
                                TableRepeater::make('referral_workers')
                                    ->label('Список направленных работников')
                                    ->relationship('incidentEmployeeReferrals')
                                    ->headers([
                                        Header::make('Должность')->width('300px')->markAsRequired(),
                                        Header::make('ФИО')->markAsRequired(),
                                        Header::make('Время направления')->markAsRequired(),
                                        Header::make('Время прибытия'),
                                    ])
                                    ->defaultItems(0)
                                    ->schema([
                                        Forms\Components\TextInput::make('position')
                                            ->label('Должность')
                                            ->datalist(fn(): array => config('incident_short_positions'))
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->required(),
                                        Forms\Components\TextInput::make('fio')
                                            ->label('ФИО')
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->required(),
                                        Forms\Components\TimePicker::make('direction_time')->seconds(false)
                                            ->label('Время направления')
                                            ->required()
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                        Forms\Components\TimePicker::make('arrival_time')->seconds(false)
                                            ->label('Время прибытия')
                                    ])
                                    ->orderable(false)
                            ]),

                        //Информирование работника
                        Forms\Components\Tabs\Tab::make('Информирование работников')
                            ->schema([
                                TableRepeater::make('information_workers')
                                    ->label('Список информированных работников')
                                    ->relationship('incidentEmployeeInformations')
                                    ->headers([
                                        Header::make('Должность')->width('300px')->markAsRequired(),
                                        Header::make('ФИО')->markAsRequired(),
                                        Header::make('Время информирования')->markAsRequired(),
                                    ])
                                    ->defaultItems(0)
                                    ->schema([
                                        Forms\Components\TextInput::make('position')
                                            ->label('Должность')
                                            ->datalist(fn(): array => config('incident_short_positions'))
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->required(),
                                        Forms\Components\TextInput::make('fio')
                                            ->label('ФИО')
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->required(),
                                        Forms\Components\TimePicker::make('information_time')->seconds(false)
                                            ->label('Время информирования')
                                            ->required()
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                    ])
                                    ->orderable(false)
                            ]),

                        //Хроника
                        Forms\Components\Tabs\Tab::make('Хроника')
                            ->schema([
                                TableRepeater::make('chronicles')
                                    ->relationship('eventChronicles')
                                    ->label('Список событий')
                                    ->headers([
                                        Header::make('Дата и время')->width('200px')->markAsRequired(),
                                        Header::make('Описание')->markAsRequired()->width('600px'),
                                        Header::make('В сводке?'),
                                    ])
                                    ->defaultItems(0)
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('datetime_event')
                                            ->seconds(false)
                                            ->default(now())
                                            ->native(false)
                                            ->required()
                                            ->displayFormat('d.m.y H:i')
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->closeOnDateSelection(),
                                        Forms\Components\Textarea::make('description')
                                            ->required()
                                            ->rows(3)
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                        Forms\Components\Checkbox::make('is_show_in_reports')
                                            ->default(true)
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->hint(function (string $operation, ?EventChronicles $record) {
                                                if ($operation === 'edit' || $operation === 'view') {
                                                    $htmlHint = '<span class="text-xs">';
                                                    if ($record?->creator) {
                                                        $htmlHint .= 'Cоздал: ' . $record->creator->name .
                                                        '<br>' . $record?->created_at?->format('d.m.y H:i') ?? '';
                                                    }
                                                    if ($record?->editor) {
                                                        $htmlHint .= '<br>Изменил: ' . $record->editor->name .
                                                        '<br>' . $record?->updated_at?->format('d.m.y H:i') ?? '';
                                                    }
                                                    $htmlHint .= '</span>';
                                                    return new HtmlString($htmlHint);
                                                }
                                                return '';
                                            }),
                                    ])
                            ]),
                        Forms\Components\Tabs\Tab::make('Дополнительная информация')
                            ->schema([
                                //Курирующий диспетчерский участок
                                Select::make('dispatch_area_id')
                                    ->label('Курирующий участок ДП')
                                    ->relationship('dispatchArea', 'name', function (Builder $query, Get $get) {
                                        $idsDispatchAreasByGroupInfObject = ObjectInfrastructure::firstWhere('id',
                                            $get('object_infrastructure_id'))
                                            ?->groupInfrastructureObject?->dispatchAreas?->pluck('id')?->toArray() ?? null;
                                        if ($idsDispatchAreasByGroupInfObject) {
                                            return $query->whereIn('id', $idsDispatchAreasByGroupInfObject);
                                        }
                                        return $query;
                                    })
                                    ->preload()
                                    ->searchable()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->required()
                                    ->disabled(function () {
                                        return !Auth::user()->hasRole(['admin', 'senior_dispatcher']);
                                    })
                                    ->dehydrated()
                                    ->exists('dispatch_areas', 'id'),

                                ToggleButtons::make('feedback')
                                    ->label('Отражать инцидент в сводке?')
                                    ->boolean()
                                    ->inline()
                                    ->default(true),

                                //Пользователь создавший инцидент
                                Forms\Components\Placeholder::make('creator')
                                    ->label('Инцидент создал')
                                    ->content(function (Incident $record) {
                                        $listText = [];
                                        if ($record->creator?->position) {
                                            $listText[] = $record->creator?->position;
                                        }
                                        if ($record->creator?->name) {
                                            $listText[] = $record->creator?->name;
                                        }
                                        return implode(' ', $listText);
                                    })
                                    ->helperText(function (Incident $record) {
                                        return $record->created_at?->format('d.m.y H:i') ?? '';
                                    })
                                    ->visible(fn(string $operation
                                    ): bool => $operation === 'edit' || $operation === 'view'),

                                //Пользователь редактировавший инцидент
                                Forms\Components\Placeholder::make('editor')
                                    ->label('Последнее редактирование')
                                    ->visible(fn(string $operation
                                    ): bool => $operation === 'edit' || $operation === 'view')
                                    ->content(function (Incident $record) {
                                        $listText = [];
                                        if ($record->editor?->position) {
                                            $listText[] = $record->editor?->position;
                                        }
                                        if ($record->editor?->name) {
                                            $listText[] = $record->editor?->name;
                                        }
                                        return implode(' ', $listText);
                                    })
                                    ->helperText(function (Incident $record) {
                                        return $record->updated_at?->format('d.m.y H:i') ?? '';
                                    }),


                                //Примечание
                                Forms\Components\Textarea::make('note')
                                    ->label('Примечание')
                                    ->rows(2)
                                    ->maxLength(1500)
                                    ->columnSpanFull()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                            ])
                            ->columns(4)
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ViewColumn::make('datetime_incident')
                    ->label('Дата и время')
                    ->view('filament.tables.incident.date_incident')
                    ->tooltip(function (Incident $record) {
                        return 'Инцидент создал: ' . $record->creator->name;
                    })
                ,

                Tables\Columns\TextColumn::make('division.short_name')
                    ->label('ЭМЧ'),

                Tables\Columns\TextColumn::make('objectInfrastructure.name')
                    ->label('Место')
                    ->formatStateUsing(function (Incident $record) {
                        return TypesInfrastructureObjectEnum::tryFrom($record->objectInfrastructure?->type ?? '')->getShortLabel() .
                            ' ' .
                            $record->objectInfrastructure->name;
                    })
                    ->description(function (Incident $record) {
                        $descriptionTextListArray = [];
                        if ($record->location) {
                            $descriptionTextListArray[] = $record->location;
                        }
                        if ($record->detail_location) {
                            $descriptionTextListArray[] = $record->detail_location;
                        }
                        return implode(' | ', $descriptionTextListArray);
                    })
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('incidentType.title')
                    ->label('Тип инцидента')
                    ->description(function (Incident $record) {
                        if ($record->incidentType->has_directory_objects) {
                            return '';
                        } else {
                            return $record->ituSpecie?->title ?? '';
                        }
                    }),

                Tables\Columns\TextColumn::make('ituCharacteristic.title')
                    ->label('Описание')
                    ->wrap()
                    ->state(function (Incident $record) {
                        if ($record->incidentType?->has_directory_objects) {
                            return $record->ituDirectoryObject?->ituSpecie?->title ?
                                $record->ituDirectoryObject?->ituSpecie?->title . '-' . $record->ituDirectoryObject?->title :
                                $record->ituDirectoryObject?->title;
                        } else {
                            return $record->ituCharacteristic?->title ?? '';
                        }
                    })
                    ->description(function (Incident $record) {
                        if ($record->incidentType->has_directory_objects) {
                            $descriptionTextListArray = [];
                            if ($record->ituCharacteristic?->title) {
                                $descriptionTextListArray[] = $record->ituCharacteristic?->title;
                            }
                            if ($record->detail_object_incident) {
                                $descriptionTextListArray[] = $record->detail_object_incident;
                            }
                            return implode(' | ', $descriptionTextListArray);
                        } else {
                            return $record->detail_object_incident;
                        }
                    }),

                Tables\Columns\ViewColumn::make('ituFault.title')
                    ->label('Неисправность')
                    ->view('filament.tables.incident.fault_element_reason_column')
                    ->tooltip(fn(Incident $record): ?string => $record->detail_incident),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('division_id')
                    ->label('Подразделение')
                    ->relationship('division', 'short_name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('object_infrastructure_id')
                    ->label('Местоположение')
                    ->relationship('objectInfrastructure', 'name', function (Builder $query) {
                        if (Auth::user()->hasRole('linear_dispatcher')) {
                            $groupInfrastructureObjectsList = Auth::user()?->dispatchArea?->groupInfrastructureObjects()?->get()?->pluck('id')?->toArray();
                            if ($groupInfrastructureObjectsList && is_array($groupInfrastructureObjectsList) && count($groupInfrastructureObjectsList) > 0) {
                                return $query->whereIn('group_infrastructure_object_id',
                                    $groupInfrastructureObjectsList)
                                    ->with('groupInfrastructureObject');
                            }
                        }
                        return $query->with('groupInfrastructureObject');
                    })
                    ->preload()
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(function (ObjectInfrastructure $record) {
                        return TypesInfrastructureObjectEnum::tryFrom($record->type)->getLabel() .
                            ' ' .
                            $record->name .
                            ' (' .
                            $record->groupInfrastructureObject->short_title .
                            ')';
                    }),
                Tables\Filters\SelectFilter::make('incident_type_id')
                    ->relationship('incidentType', 'title')
                    ->preload()
                    ->searchable()
                    ->label('Тип инцидента')

            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->modalWidth(MaxWidth::SevenExtraLarge)
                        ->stickyModalFooter(),
                    Tables\Actions\EditAction::make()
                        ->modalWidth(MaxWidth::SevenExtraLarge)
                        ->stickyModalFooter(),
                ])
            ])
            ->recordAction(Tables\Actions\EditAction::class)
            ->defaultSort('datetime_incident', 'desc')
            ->defaultPaginationPageOption(25)
            ->description('Данные за сутки отражаются с 8 утра предыдущего дня с момента запроса данных на сервере');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
        ];
    }

}
