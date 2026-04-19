<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource\RelationManagers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\PermissionRegistrar;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';
    protected static ?string $label = 'Право доступа';
    protected static ?string $pluralLabel = 'Права доступа';
    protected static ?string $title = 'Права доступа';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Наименование права на английском')
                    ->maxLength(255)
                    ->required()
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $stringUpdated = $state ?? '';
                        $stringUpdated = trim($stringUpdated);
                        $stringUpdated = function_exists('mb_strtolower') ? mb_strtolower($stringUpdated) : strtolower($stringUpdated);
                        $stringUpdated = strtr($stringUpdated, Config::get('russian_translit'));
                        $stringUpdated = str_replace(' ', '_', $stringUpdated);
                        $set('name', $stringUpdated);
                    })
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
                TextInput::make('description')
                    ->label('Описание права на русском')
                    ->maxLength(255)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
                TextInput::make('guard_name')
                    ->label('Защитник')
                    ->required()
                    ->default('web')
                    ->visible(fn() => Auth::user()->hasRole('admin'))
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Наименование права')
                    ->copyable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Описание права')
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                AttachAction::make()
                    ->preloadRecordSelect(config('filament-authentication.preload_permissions', true))
                    ->recordSelect(fn($select) => $select->multiple())
                    ->closeModalByClickingAway(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function afterAttach(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function afterDetach(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
