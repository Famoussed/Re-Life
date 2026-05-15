<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\AnimalSpecies;
use App\Enums\Gender;
use App\Models\Animal;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Hayvanlar — Re·Life')]
class AnimalManager extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $species = '';

    public string $age_estimate = '';

    public string $gender = '';

    public string $story = '';

    public string $health_status = '';

    public bool $is_active = true;

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'species' => ['required', 'in:'.implode(',', array_column(AnimalSpecies::cases(), 'value'))],
            'age_estimate' => ['nullable', 'string', 'max:60'],
            'gender' => ['required', 'in:'.implode(',', array_column(Gender::cases(), 'value'))],
            'story' => ['nullable', 'string', 'max:5000'],
            'health_status' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Hayvanın adı zorunludur.',
            'species.required' => 'Tür seçimi zorunludur.',
            'species.in' => 'Geçerli bir tür seçin.',
            'gender.required' => 'Cinsiyet seçimi zorunludur.',
            'gender.in' => 'Geçerli bir cinsiyet seçin.',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $animal = Animal::findOrFail($id);

        $this->editingId = $animal->id;
        $this->name = $animal->name;
        $this->species = $animal->species->value;
        $this->age_estimate = (string) ($animal->age_estimate ?? '');
        $this->gender = $animal->gender->value;
        $this->story = (string) ($animal->story ?? '');
        $this->health_status = (string) ($animal->health_status ?? '');
        $this->is_active = (bool) $animal->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId !== null) {
            Animal::findOrFail($this->editingId)->update($data);
            session()->flash('message', 'Hayvan bilgileri güncellendi.');
        } else {
            $data['shelter_id'] = auth()->user()->shelter->id;
            Animal::create($data);
            session()->flash('message', 'Yeni hayvan eklendi.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Animal::findOrFail($id)->delete();
        session()->flash('message', 'Hayvan silindi.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'species', 'age_estimate', 'gender', 'story', 'health_status']);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.admin.animal-manager', [
            'animals' => Animal::latest()->get(),
        ]);
    }
}
