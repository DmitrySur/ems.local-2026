<?php

namespace App\Filament\Resources\Directories;

use App\Filament\Resources\Directories\IncidentTypeResource\Pages;
use App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers\ItuCharacteristicsRelationManager;
use App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers\ItuDirectoryObjectsRelationManager;
use App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers\ItuElementsRelationManager;
use App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers\ItuFaultsRelationManager;
use App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers\ItuSpeciesRelationManager;
use App\Models\Directories\IncidentType;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncidentTypeResource extends Resource
{
    protected static ?string $model = IncidentType::class;
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $navigationLabel = 'Типы инцидентов';
    protected static ?string $label = 'Тип инцидиентов';
    protected static ?string $pluralLabel = 'Типы инцидиентов';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Наименование типа')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),
                Forms\Components\Tabs::make('main_tabs')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Tabs\Tab::make('Отображение связанных данных')
                            ->columns(3)
                            ->schema([
                                ToggleButtons::make('has_species')
                                    ->label('Наличие видов')
                                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                                    ->default(false)
                                    ->in([0, 1])
                                    ->boolean()
                                    ->inline(),
                                ToggleButtons::make('has_characteristic')
                                    ->label('Наличие характеристики')
                                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                                    ->default(false)
                                    ->in([0, 1])
                                    ->boolean()
                                    ->inline(),
                                ToggleButtons::make('has_elements')
                                    ->label('Наличие элементов')
                                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                                    ->default(false)
                                    ->in([0, 1])
                                    ->boolean()
                                    ->inline(),
                                ToggleButtons::make('has_faults')
                                    ->label('Наличие неисправностей')
                                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                                    ->default(false)
                                    ->in([0, 1])
                                    ->boolean()
                                    ->inline(),
                                ToggleButtons::make('has_directory_objects')
                                    ->label('Наличие справочника объектов')
                                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                                    ->default(false)
                                    ->in([0, 1])
                                    ->boolean()
                                    ->inline(),

                            ]),
                        Forms\Components\Tabs\Tab::make('Сведения для поля "сообщил"')
                            ->schema([
                                Repeater::make('reported_by_list')
                                    ->label('Возможные значения для поля "сообщил"')
                                    ->simple(TextInput::make('text')
                                        ->required()
                                        ->validationAttribute(fn(Component $component
                                        ): string => $component->getLabel())
                                        ->label('Текст для поля "сообщил"')
                                        ->maxLength(300)
                                    )
                                    ->defaultItems(0)
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Наименование')->wrap(),
                TextColumn::make('report_position')->label('Позициия в отчетах'),
                IconColumn::make('has_species')->boolean()->label('Виды'),
                IconColumn::make('has_characteristic')->boolean()->label('Хар-ки'),
                IconColumn::make('has_elements')->boolean()->label('Элементы'),
                IconColumn::make('has_faults')->boolean()->label('Неисправности'),
                IconColumn::make('has_directory_objects')->boolean()->label('Перечень'),
            ])
            ->reorderable('report_position')
            ->defaultSort('report_position')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //TODO Защита от удаления вложенных записей
                Tables\Actions\DeleteAction::make()
            ])
            ->defaultPaginationPageOption(50);
    }

    public static function getRelations(): array
    {
        return [
            ItuSpeciesRelationManager::class,
            ItuCharacteristicsRelationManager::class,
            ItuDirectoryObjectsRelationManager::class,
            ItuElementsRelationManager::class,
            ItuFaultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidentTypes::route('/'),
            'view' => Pages\ViewIncidentTypes::route('/{record}'),

        ];
    }

}
