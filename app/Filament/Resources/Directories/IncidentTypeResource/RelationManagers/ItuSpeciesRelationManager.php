<?php

namespace App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class ItuSpeciesRelationManager extends RelationManager
{
    protected static string $relationship = 'ituSpecies';
    protected static ?string $title = 'Виды ИТУ';
    protected static ?string $label = 'Вид ИТУ';
    protected static ?string $pluralLabel = 'Виды ИТУ';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Наименование вида')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),
                ToggleButtons::make('has_directory_objects')
                    ->label('Вид включен в справочник')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->default(0)
                    ->in([0, 1])
                    ->boolean()
                    ->inline(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Наименование'),
                IconColumn::make('has_directory_objects')->boolean()->label('Наличие справочника'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ]);
    }
}
