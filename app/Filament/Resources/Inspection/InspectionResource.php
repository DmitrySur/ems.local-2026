<?php

namespace App\Filament\Resources\Inspection;

use App\Enum\InspectionsTypes;
use App\Enum\TypesInfrastructureObjectEnum;
use App\Filament\Resources\Inspection\InspectionResource\Pages;
use App\Models\Directories\ObjectInfrastructure;
use App\Models\Inspection\Inspection;
use App\Models\Inspection\Inspector;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Stevebauman\Purify\Facades\Purify;
use Throwable;

class InspectionResource extends Resource
{
    protected static ?string $model = Inspection::class;
    protected static ?string $label = 'Проверка';
    protected static ?string $pluralLabel = 'Проверки';
    protected static ?string $navigationLabel = 'Проверки';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Тип проверки')
                            ->options(InspectionsTypes::class)
                            ->enum(InspectionsTypes::class)
                            ->native(false)
                            ->reactive()
                            ->required(),
                        Forms\Components\DatePicker::make('date_start')
                            ->label('Дата проверки')
                            ->reactive()
                            ->required()
                            ->format('d.m.Y'),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Время начала проверки')
                            ->required()
                            ->native(false)
                            ->required()
                            ->seconds(false),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Время окончания проверки')
                            ->required()
                            ->native(false)
                            ->required()
                            ->seconds(false),
                        Forms\Components\Select::make('division_id')
                            ->label('Подразделение')
                            ->relationship('division', 'short_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('position')
                            ->label('Должность')
                            ->options(fn() => array_combine(config('inspection_short_positions'),
                                config('inspection_short_positions')))
                            ->searchable()
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('inspector_id')
                            ->label('ФИО проверяющего')
                            ->required()
                            ->relationship(name: 'inspector', titleAttribute: 'full_name')
                            ->searchable(['full_name'])
                            ->preload()
                            ->columnSpan(2)
                            ->helperText('В скобках должность по умолчанию')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Полное ФИО')
                                    ->required()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->maxLength(225),
                                Forms\Components\TextInput::make('short_name')
                                    ->label('Краткое ФИО')
                                    ->required()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->maxLength(225),
                                Forms\Components\TextInput::make('default_position')
                                    ->label('Должность')
                                    ->required()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->maxLength(225),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('full_name')
                                    ->label('Полное ФИО')
                                    ->required()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->maxLength(225),
                                Forms\Components\TextInput::make('short_name')
                                    ->label('Краткое ФИО')
                                    ->required()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->maxLength(225),
                                Forms\Components\TextInput::make('default_position')
                                    ->label('Должность')
                                    ->required()
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->maxLength(225),
                            ])
                        ,
                        Forms\Components\Repeater::make('subdivisions')
                            ->label('Проверяемые участки')
                            ->columnSpanFull()
                            ->minItems(1)
                            ->required()
                            ->simple(
                                Forms\Components\TextInput::make('subdivision')
                                    ->label('Наименование участка')
                                    ->validationAttribute(fn(Component $component
                                    ): string => $component->getLabel())
                                    ->required())
                            ->required(),
                        Forms\Components\Repeater::make('infObjects')
                            ->label('Проверяемые объекты инфраструктуры')
                            ->relationship('objectInfrastructureInspectionItems')
                            ->columnSpanFull()
                            ->minItems(1)
                            ->required()
                            ->simple(//Объект инфраструктуры
                                Forms\Components\Select::make('object_infrastructure_id')
                                    ->label('Объект')
                                    ->relationship('objectInfrastructure', 'name', function (Builder $query) {
                                        return $query->with('groupInfrastructureObject');
                                    })
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
                                    ->required()
                                    ->exists('object_infrastructures', 'id'))
                            ->required(),
                    ])
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_start')
                    ->label('Дата проверки')
                    ->date('d.m.Y')
                    ->prefix(function (Inspection $record) {
                        if ($record->type === InspectionsTypes::NightInspection->value) {
                            return 'в ночь на ';
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('division.short_name')
                    ->wrap()
                    ->label('Подразделение'),
                Tables\Columns\TextColumn::make('type')
                    ->wrap()
                    ->label('Тип проверки')
                    ->formatStateUsing(fn(?string $state
                    ): string => InspectionsTypes::tryFrom($state)->getLabel() ?? ''),
                Tables\Columns\TextColumn::make('inspector.short_name')
                    ->wrap()
                    ->label('ФИО, должность')
                    ->description(function (Inspection $record) {
                        return $record->position;
                    }),
                Tables\Columns\TextColumn::make('objectInfrastructures.name')
                    ->label('Местонахождения')
                    ->listWithLineBreaks()
                    ->wrap()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth(MaxWidth::SevenExtraLarge)
                    ->modalDescription('Для ночной проверки указывается дата, начинающаяся с 00 часов!'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(50)
            ->filters([
                DateRangeFilter::make('date_start')
                    ->label('Диапозон дат проверок')
                    ->startDate(Carbon::now()->subDay())
                    ->endDate(Carbon::now()),
                Tables\Filters\SelectFilter::make('division')
                    ->label('Подразделение')
                    ->relationship('division', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('position')
                    ->label('Должность')
                    ->options(fn() => array_combine(config('inspection_short_positions'),
                        config('inspection_short_positions')))
                    ->searchable()

            ], layout: FiltersLayout::AboveContent);
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
            'index' => Pages\ListInspections::route('/'),
        ];
    }
}
