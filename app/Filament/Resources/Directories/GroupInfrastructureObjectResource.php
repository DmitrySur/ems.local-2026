<?php

namespace App\Filament\Resources\Directories;

use App\Filament\Resources\Directories\GroupInfrastructureObjectResource\Pages;
use App\Filament\Resources\Directories\GroupInfrastructureObjectResource\RelationManagers\ObjectInfrastructuresRelationManager;
use App\Models\Directories\GroupInfrastructureObject;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupInfrastructureObjectResource extends Resource
{
    protected static ?string $model = GroupInfrastructureObject::class;
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $label = 'Группа объектов инфраструктуры';
    protected static ?string $pluralLabel = 'Группы объектов инфраструктуры';
    protected static ?string $navigationLabel = 'Объекты инфраструктуры';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Наименование')
                    ->maxLength(300)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
                TextInput::make('short_title')
                    ->label('Краткое наименование')
                    ->maxLength(125)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Наименование'),
                Tables\Columns\TextColumn::make('short_title')
                    ->label('Краткое наименование'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ObjectInfrastructuresRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroupInfrastructureObjects::route('/'),
            'view' => Pages\ViewGroupInfrastructureObject::route('/{record}'),
        ];
    }


}
