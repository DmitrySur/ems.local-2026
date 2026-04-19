<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources;

use App\Filament\Clusters\UsersAndPermissionCluster;
use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource\RelationManagers\PermissionsRelationManager;
use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource\RelationManagers\UsersRelationManager;
use Auth;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Phpsa\FilamentAuthentication\Resources\RoleResource;
use Spatie\Permission\Models\Role;

class CustomRoleUserResource extends RoleResource
{
    protected static ?string $model = Role::class;
    protected static ?string $cluster = UsersAndPermissionCluster::class;
    protected static ?string $navigationLabel = 'Роли и права доступа';
    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'custom-role-users';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Наименование роли на английском')
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->afterStateUpdated(function (string $state, Set $set) {
                        $stringUpdated = $state;
                        $stringUpdated = trim($stringUpdated);
                        $stringUpdated = function_exists('mb_strtolower') ? mb_strtolower($stringUpdated) : strtolower($stringUpdated);
                        $stringUpdated = strtr($stringUpdated, Config::get('russian_translit'));
                        $stringUpdated = str_replace(' ', '_', $stringUpdated);
                        $set('name', $stringUpdated);
                    }),
                TextInput::make('description')
                    ->label('Описание роли на русском')
                    ->maxLength(250)
                    ->required()
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),
                TextInput::make('guard_name')
                    ->required()
                    ->default('web')
                    ->visible(fn() => Auth::user()->hasRole('admin'))
                    ->validationAttribute(fn(Component $component): string => $component->getLabel()),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guard_name')
                    ->visible(fn() => Auth::user()->hasRole('admin')),
                TextColumn::make('name')
                    ->label('Наименование роли')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('description')
                    ->label('Описание роли')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => UsersAndPermissionCluster\Resources\CustomRoleUserResource\Pages\ListCustomRoleUsers::route('/'),
            'create' => UsersAndPermissionCluster\Resources\CustomRoleUserResource\Pages\CreateCustomRoleUser::route('/create'),
            'edit' => UsersAndPermissionCluster\Resources\CustomRoleUserResource\Pages\EditCustomRoleUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getRelations(): array
    {
        return [
            PermissionsRelationManager::class,
            UsersRelationManager::class,
        ];
    }
}
