<?php

declare(strict_types=1);

namespace App\Livewire\Shelter;

use App\Models\Shelter\Shelter;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Barınak Profili — Re·Life')]
class ShelterProfileEdit extends Component
{
    public string $name = '';

    public string $city = '';

    public string $phone = '';

    public string $address = '';

    public string $licenseNo = '';

    public string $statusLabel = '';

    public function mount(): void
    {
        $shelter = auth()->user()->shelter;

        $this->name = (string) $shelter->name;
        $this->city = (string) $shelter->city;
        $this->phone = (string) ($shelter->phone ?? '');
        $this->address = (string) ($shelter->address ?? '');
        $this->licenseNo = (string) $shelter->license_no;
        $this->statusLabel = $shelter->status->label();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'city' => ['required', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Barınak adı zorunludur.',
            'city.required' => 'Şehir zorunludur.',
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        /** @var Shelter $shelter */
        $shelter = auth()->user()->shelter;
        $shelter->update($data);

        session()->flash('message', 'Barınak bilgileri güncellendi.');
    }

    public function render(): View
    {
        return view('livewire.shelter.shelter-profile-edit');
    }
}
