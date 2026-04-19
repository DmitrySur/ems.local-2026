<?php

namespace App\Filament\Resources\Directories\GroupInfrastructureObjectResource\RelationManagers;

use App\Enum\TypesInfrastructureObjectEnum;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ObjectInfrastructuresRelationManager extends RelationManager
{
    protected static string $relationship = 'objectInfrastructures';
    protected static ?string $title = 'Объекты инфраструктуры';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Тип объекта')
                    ->options(TypesInfrastructureObjectEnum::class)
                    ->searchable()
                    ->enum(TypesInfrastructureObjectEnum::class)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
                Forms\Components\TextInput::make('name')
                    ->label('Наименование')
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Тип объекта')
                    ->formatStateUsing(fn(string $state
                    ): string => TypesInfrastructureObjectEnum::tryFrom($state)?->getLabel() ?? ''),
                Tables\Columns\TextColumn::make('name')
                    ->label('Наименование')
                    ->searchable()
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

    public function isReadOnly(): bool
    {
        return false;
    }
}
