<?php

namespace App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItuCharacteristicsRelationManager extends RelationManager
{
    protected static string $relationship = 'ituCharacteristics';
    protected static ?string $title = 'Характеристики ИТУ';
    protected static ?string $label = 'Характеристика ИТУ';
    protected static ?string $pluralLabel = 'Характеристики ИТУ';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Наименование характеристики')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Наименование'),
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

            ]);
    }
}
