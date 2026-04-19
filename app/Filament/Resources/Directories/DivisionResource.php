<?php

namespace App\Filament\Resources\Directories;

use App\Filament\Resources\Directories\DivisionResource\Pages;
use App\Models\Directories\Division;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $slug = 'directories/divisions';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $label = 'Подразделение';
    protected static ?string $pluralLabel = 'Подразделения';
    protected static ?string $navigationLabel = 'Подразделения';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Полнование наименование')
                    ->required()
                    ->maxLength(400)
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->columnSpanFull(),
                TextInput::make('short_name')
                    ->label('Краткое наименование')
                    ->required()
                    ->maxLength(50)
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->columnSpanFull(),
                ToggleButtons::make('has_group_object')
                    ->label('Привязвка в группе объектов инфраструктуры')
                    ->boolean()
                    ->inline()
                    ->live()
                    ->default(false)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
                Select::make('group_infrastructure_object_id')
                    ->label('Группа объектов инфраструктуры')
                    ->relationship(name: 'groupInfrastructureObject', titleAttribute: 'short_title')
                    ->preload()
                    ->searchable()
                    ->visible(fn(Get $get) => $get('has_group_object'))
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Полное наименование')
                    ->wrap(),
                TextColumn::make('short_name')
                    ->label('Краткое наименование'),
                TextColumn::make('report_position')
                    ->label('Позиция в отчетах'),
                TextColumn::make('groupInfrastructureObject.title')
                    ->label('Привязка к группе')
                    ->default('-')
            ])
            ->reorderable('report_position')
            ->defaultSort('report_position')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisions::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
