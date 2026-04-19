<?php

namespace App\Filament\Resources\Directories\IncidentTypeResource\RelationManagers;

use App\Models\Directories\ItuSpecie;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentClusters\Forms\Cluster;
use Illuminate\Validation\Rules\Unique;

class ItuDirectoryObjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'ituDirectoryObjects';
    protected static ?string $title = 'Объекты ИТУ';
    protected static ?string $label = 'Объект ИТУ';
    protected static ?string $pluralLabel = 'Объекты ИТУ';

    public function isReadOnly(): bool
    {
        return false;
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Cluster::make([
                    Select::make('itu_specie_id')
                        ->label('Вид ИТУ')
                        ->searchable()
                        ->exists('itu_species', 'id')
                        ->live()
                        ->options(function () {
                            return ItuSpecie::where([
                                'incident_type_id' => $this->getOwnerRecord()['id'],
                                'has_directory_objects' => true
                            ])
                                ->get()
                                ->pluck('title', 'id')
                                ->toArray();
                        }),
                    TextInput::make('title')
                        ->prefix('№')
                        ->label('Номер')
                        ->validationAttribute(fn(Component $component): string => $component->getLabel())
                        ->required()
                        ->maxLength(150)
                        ->dehydrateStateUsing(fn(string $state): string => str_replace(' ', '', $state))
                        ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Forms\Get $get) {
                            return $rule
                                ->where('incident_type_id', $this->getOwnerRecord()['id'])
                                ->where('itu_specie_id', $get('itu_specie_id'));
                        })
                ])
                    ->label('Вид и номер ИТУ')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('ituSpecie.title')->label('Вид ИТУ'),
                Tables\Columns\TextColumn::make('title')->label('Номер'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->visible(function () {
                    $checkRecord = ItuSpecie::where([
                        'incident_type_id' => $this->getOwnerRecord()['id'],
                        'has_directory_objects' => true
                    ])->get();
                    if ($checkRecord->count() > 0) {
                        return true;
                    }
                    return false;
                }),
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
