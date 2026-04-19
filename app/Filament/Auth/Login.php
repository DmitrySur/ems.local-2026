<?php

namespace App\Filament\Auth;

use App\Models\Directories\DispatchArea;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;

class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form->schema([
            $this->getLoginFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getDispatchAreaFormComponent(),
            $this->getRememberFormComponent(),
        ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Логин польователя')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->required();
    }

    protected function getDispatchAreaFormComponent(): Component
    {
        return Select::make('dispatch_area_id')
            ->label('Диспетчерский участок')
            ->options(DispatchArea::select(['id', 'name'])->pluck('name', 'id'))
            ->native(false)
            ->helperText('Поле обязательно к заполнению для линейных диспетчеров.')
            ->required(function (Get $get) {
                return User::firstWhere('login', '=', $get('login'))?->hasRole('linear_dispatcher');
            });
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'login' => $data['login'],
            'password' => $data['password'],

        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $exception) {
            if (array_key_exists('data.dispatch_area_id',
                $exception->validator?->getMessageBag()?->getMessages() ?? [])) {
                throw ValidationException::withMessages([
                    'data.dispatch_area_id' => 'Поле обязательно для заполнения',
                ]);
            }
            throw ValidationException::withMessages([
                'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }
    }
}
