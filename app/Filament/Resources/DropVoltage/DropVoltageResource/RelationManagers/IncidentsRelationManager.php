<?php

namespace App\Filament\Resources\DropVoltage\DropVoltageResource\RelationManagers;

use App\Filament\Resources\Incident\IncidentResource;
use App\Models\Incident\Incident;
use Exception;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class IncidentsRelationManager extends RelationManager
{
    protected static string $relationship = 'incidents';

    public function isReadOnly(): bool
    {
        return false;
    }

    protected static ?string $title = 'Список инцидентов';

    public function form(Form $form): Form
    {
        return $form
            ->schema(IncidentResource::form($form)->getComponents());
    }

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns(IncidentResource::table($table)->getColumns())
            ->description('Инциденты, связанные с текущей посадкой напряжения')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->fillForm([
                        'reported_by' => 'Посадка № ' . $this->getOwnerRecord()['id']
                    ])
                    ->modalWidth(MaxWidth::SevenExtraLarge)
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth(MaxWidth::SevenExtraLarge),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
