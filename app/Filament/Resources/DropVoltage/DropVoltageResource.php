<?php

namespace App\Filament\Resources\DropVoltage;

use App\Enum\DropVoltageDeviceStatuses;
use App\Enum\DropVoltageDeviceTypes;
use App\Enum\DropVoltageStatuses;
use App\Filament\Resources\DropVoltage\DropVoltageResource\Pages\ListDropVoltages;
use App\Filament\Resources\DropVoltage\DropVoltageResource\Pages\ViewDropVoltage;
use App\Filament\Resources\DropVoltage\DropVoltageResource\RelationManagers\IncidentsRelationManager;
use App\Models\DropVoltage\DropVoltage;
use App\Models\DropVoltage\DropVoltageEventChronicles;
use App\Models\Incident\EventChronicles;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Exception;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class DropVoltageResource extends Resource
{
    protected static ?string $model = DropVoltage::class;
    protected static ?string $label = 'Посадка напряжения';
    protected static ?string $pluralLabel = 'Посадки напряжения';

    protected static ?string $navigationLabel = 'Посадки';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make('3')
                    ->schema([
                        //Дата и время посадки
                        DateTimePicker::make('datetime_drop')
                            ->label('Дата и время')
                            ->seconds(false)
                            ->native(false)
                            ->displayFormat('d.m.Y H:i')
                            ->default(now())
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                            ->required()
                            ->closeOnDateSelection(),
                        Select::make('group_infrastructure_object_id')
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                            ->label('Группа объектов')
                            ->relationship('groupInfrastructureObject', 'title')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->exists('group_infrastructure_objects', 'id'),
                        ToggleButtons::make('status_drop')
                            ->label('Статус посадки')
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                            ->required()
                            ->options([
                                DropVoltageStatuses::Closed->value => DropVoltageStatuses::Closed->getLabel(),
                                DropVoltageStatuses::Opened->value => DropVoltageStatuses::Opened->getLabel(),
                            ])
                            ->colors([
                                DropVoltageStatuses::Closed->value => 'success',
                                DropVoltageStatuses::Opened->value => 'danger'
                            ])
                            ->default(DropVoltageStatuses::Opened->value)
                            ->inline(),
                    ]),
                Grid::make()
                    ->schema([
                        Textarea::make('detail_location')
                            ->label('Участок посадки')
                            ->rows(3)
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                            ->maxLength(400)
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                            ->required(),
                        Textarea::make('detail_drop')
                            ->label('Описание посадки')
                            ->rows(3)
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                            ->maxLength(400)
                            ->validationAttribute(fn(Component $component
                            ): string => $component->getLabel())
                    ]),
                Tabs::make('tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Список устройств')
                            ->schema([
                                TableRepeater::make('drop_voltage_devices')
                                    ->relationship('dropVoltageDevices')
                                    ->label('Список отключившихся устройств')
                                    ->minItems(1)
                                    ->defaultItems(1)
                                    ->headers([
                                        Header::make('Тип устройства')
                                            ->width('150px')
                                            ->markAsRequired(),
                                        Header::make('Номер/Привязка')
                                            ->markAsRequired(),
                                        Header::make('Статус')
                                            ->markAsRequired(),
                                        Header::make('Примечание'),
                                    ])
                                    ->schema([
                                        Select::make('type')
                                            ->options(DropVoltageDeviceTypes::class)
                                            ->label('Тип')
                                            ->placeholder('Выбрать')
                                            ->native(false)
                                            ->required()
                                            ->enum(DropVoltageDeviceTypes::class)
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                        TextInput::make('name')
                                            ->required()
                                            ->label('Наименование')
                                            ->maxLength(355)
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                        ToggleButtons::make('status')
                                            ->options(DropVoltageDeviceStatuses::class)
                                            ->default(DropVoltageDeviceStatuses::Unchecked->value)
                                            ->grouped()
                                            ->required()
                                            ->label('Статус')
                                            ->enum(DropVoltageDeviceStatuses::class)
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                        TextInput::make('comment')
                                            ->maxLength(255)
                                            ->label('Комментарий')
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel()),
                                    ])
                            ]),
                        Tabs\Tab::make('Хроника')
                            ->schema([
                                TableRepeater::make('drop_voltage_chronicles')
                                    ->relationship('dropVoltageEventChronicles')
                                    ->label('Список событий')
                                    ->headers([
                                        Header::make('Дата и время')->width('200px')->markAsRequired(),
                                        Header::make('Описание')->markAsRequired(),
                                    ])
                                    ->defaultItems(0)
                                    ->schema([
                                        DateTimePicker::make('datetime_event')
                                            ->seconds(false)
                                            ->default(now())
                                            ->native(false)
                                            ->required()
                                            ->displayFormat('d.m.y H:i')
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->closeOnDateSelection(),
                                        Textarea::make('description')
                                            ->required()
                                            ->rows(3)
                                            ->validationAttribute(fn(Component $component
                                            ): string => $component->getLabel())
                                            ->helperText(function (string $operation, ?DropVoltageEventChronicles $record) {
                                                if ($operation === 'edit' || $operation === 'view') {
                                                    $htmlHint = '<span class="text-xs">';
                                                    if ($record?->creator) {
                                                        $htmlHint .= 'Cоздал: ' . $record->creator->name .
                                                        ' ' . $record?->created_at?->format('d.m.y H:i') ?? '';
                                                    }
                                                    if ($record?->editor) {
                                                        $htmlHint .= ' Изменил: ' . $record->editor->name .
                                                        ' ' . $record?->updated_at?->format('d.m.y H:i') ?? '';
                                                    }
                                                    $htmlHint .= '</span>';
                                                    return new HtmlString($htmlHint);
                                                }
                                                return '';
                                            }),
                                    ])
                                    ->columnSpanFull()
                            ])
                    ])
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->description('Данные за сутки отражаются с 8 утра предыдущего дня с момента запроса данных на сервере')
            ->columns([
                Tables\Columns\ViewColumn::make('datetime_incident')
                    ->label('Дата и время')
                    ->view('filament.tables.drop_voltage.date_drop')
                    ->tooltip(function (DropVoltage $record) {
                        return 'Посадку создал: ' . $record->creator->name;
                    })
                ,
                Tables\Columns\TextColumn::make('groupInfrastructureObject.title')
                    ->label('Группа объектов')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->wrap(),
                Tables\Columns\TextColumn::make('detail_location')
                    ->label('Участок посадки')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->wrap(),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withCount('incidents')
                ->with(['creator']))
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->modalWidth(MaxWidth::SevenExtraLarge),
                Tables\Actions\Action::make('CloseDropVoltage')
                    ->label('Закрыть')
                    ->visible(function (DropVoltage $record) {
                        return auth()->user()->can('drop_voltage_manage') || auth()->user()->hasRole('admin')
                            & $record->status_drop === DropVoltageStatuses::Opened->value;
                    })
                    ->action(function (Tables\Actions\Action $action, DropVoltage $record) {
                        $action->failureNotificationTitle('11111');
                        $action->failure();

                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            IncidentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDropVoltages::route('/'),
            'view' => ViewDropVoltage::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
