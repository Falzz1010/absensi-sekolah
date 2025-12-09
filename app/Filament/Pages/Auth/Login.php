<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'admin@example.com',
            'password' => 'password',
            'remember' => true,
        ]);
    }
    
    protected function getRedirectUrl(): string
    {
        // Force redirect to admin panel after login
        return route('filament.admin.pages.dashboard');
    }
}
