<?php

namespace App\Filament\Resources\Directories;

use App\Filament\Resources\Directories\DispatchAreaResource\Pages;
use App\Models\Directories\DispatchArea;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DispatchAreaResource extends Resource
{
    protected static ?string $model = DispatchArea::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Справочники';
    protected static ?string $label = 'Диспетчерский участок';
    protected static ?string $pluralLabel = 'Диспетчерские участки';
    protected static ?string $navigationLabel = 'Список дисп. участков';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Наименование')
                    ->maxLength(125)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->columnSpanFull(),
                Forms\Components\Select::make('groupInfrastructureObjects')
                    ->label('Группы объектов инфрастрктуры')
                    ->relationship(name: 'groupInfrastructureObjects', titleAttribute: 'short_title')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Наименование'),
                Tables\Columns\TextColumn::make('groupInfrastructureObjects.short_title')
                    ->label('Группы объектов инфраструктуры')
                    ->listWithLineBreaks()
                    ->bulleted()
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDispatchAreas::route('/'),
        ];
    }
}
