<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Services\RegisterShelterAdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Barınak Kaydı — Re·Life')]
class RegisterShelterAdmin extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $shelter_name = '';

    public string $license_no = '';

    public string $city = '';

    public string $phone = '';

    public string $address = '';

    public function register(RegisterShelterAdminService $service): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'shelter_name' => 'required|string|max:255',
            'license_no' => 'required|string|max:255|unique:shelters,license_no',
            'city' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:500',
        ], [], [
            'name' => 'ad soyad',
            'email' => 'e-posta',
            'password' => 'şifre',
            'shelter_name' => 'barınak adı',
            'license_no' => 'ruhsat numarası',
            'city' => 'şehir',
            'phone' => 'telefon',
            'address' => 'adres',
        ]);

        $service->register($data);

        session()->flash('status', 'Başvurun alındı! Barınağın platform yöneticisi tarafından onaylandıktan sonra giriş yapabilirsin.');

        $this->redirectRoute('login', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.auth.register-shelter-admin');
    }
}
