<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources;

use App\Filament\Clusters\UsersAndPermissionCluster;
use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource\Pages\ListCustomUsers;
use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Phpsa\FilamentAuthentication\Actions\ImpersonateLink;
use Phpsa\FilamentAuthentication\Resources\UserResource;

class CustomUserResource extends UserResource
{
    protected static ?string $model = User::class;
    protected static ?string $label = 'Пользователь';
    protected static ?string $cluster = UsersAndPermissionCluster::class;
    protected static ?string $pluralLabel = 'Пользователи';

    protected static ?string $slug = 'users-and-permission-resources/custom-users';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.name')))
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->maxLength(255)
                    ->required(),
                TextInput::make('login')
                    ->label('Логин')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->maxLength(255)
                    ->required()
                    ->unique(table: User::class, ignorable: fn(?User $record): ?User => $record),
                TextInput::make('email')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->required()
                    ->email()
                    ->maxLength(255)
                    ->unique(table: User::class, ignorable: fn(?User $record): ?User => $record)
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.email'))),
                TextInput::make('position')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->label('Должность пользователя')
                    ->maxLength(255),
                TextInput::make('password')
                    ->same('passwordConfirmation')
                    ->password()
                    ->maxLength(255)
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->required(fn($component, $get, $livewire, $model, $record, $set, $state) => $record === null)
                    ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : '')
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.password')))
                    ->hiddenOn(['view', 'edit']),
                TextInput::make('passwordConfirmation')
                    ->password()
                    ->dehydrated(false)
                    ->maxLength(255)
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->label(strval(__('filament-authentication::filament-authentication.field.user.confirm_password')))
                    ->hiddenOn(['view', 'edit']),
                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'description')
                    ->validationAttribute(fn(Component $component): string => $component->getLabel())
                    ->columnSpanFull()
                    ->preload(config('filament-authentication.preload_roles'))
                    ->label('Роли')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(strval(__('filament-authentication::filament-authentication.field.id'))),
                TextColumn::make('login')
                    ->searchable()
                    ->label('Логин'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()->label(strval(__('filament-authentication::filament-authentication.field.user.name')))
                    ->description(fn($record): string => $record->position ?? ''),
                TextColumn::make('dispatchArea.name')
                    ->label('Дисп. уч.')
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    EditAction::make('edit_password')
                        ->label('Изменить пароль')
                        ->form([
                            TextInput::make('password')
                                ->same('passwordConfirmation')
                                ->password()
                                ->maxLength(255)
                                ->required(fn(
                                    $component,
                                    $get,
                                    $livewire,
                                    $model,
                                    $record,
                                    $set,
                                    $state
                                ) => $record === null)
                                ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : '')
                                ->label(strval(__('filament-authentication::filament-authentication.field.user.password'))),
                            TextInput::make('passwordConfirmation')
                                ->password()
                                ->dehydrated(false)
                                ->maxLength(255)
                                ->label(strval(__('filament-authentication::filament-authentication.field.user.confirm_password')))

                        ]),
                    EditAction::make('dispatch_areas')
                        ->label('Привязать дисп. уч.')
                        ->modalHeading('Привязать диспетчерские участки')
                        ->form([
                            Select::make('dispatchArea')
                                ->relationship('dispatchArea', 'name')
                                ->label('Диспетчерский участок')
                                ->validationAttribute(fn(Component $component): string => $component->getLabel())
                                ->preload()
                                ->searchable()
                        ])
                        ->visible(function (User $record) {
                            return $record->hasRole('linear_dispatcher');
                        }),
                    ImpersonateLink::make()->label('Войти'),
                    DeleteAction::make(),
                ])
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
            'index' => ListCustomUsers::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
