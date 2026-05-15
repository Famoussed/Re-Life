<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\NeedStatus;
use App\Enums\NeedType;
use App\Models\Animal;
use App\Models\Need;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('İhtiyaçlar — Re·Life')]
class NeedManager extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $animal_id = '';

    public string $type = '';

    public string $title = '';

    public string $description = '';

    public string $target_amount = '';

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'animal_id' => ['required', 'exists:animals,id'],
            'type' => ['required', 'in:'.implode(',', array_column(NeedType::cases(), 'value'))],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'animal_id.required' => 'Hayvan seçimi zorunludur.',
            'animal_id.exists' => 'Geçerli bir hayvan seçin.',
            'type.required' => 'İhtiyaç türü zorunludur.',
            'type.in' => 'Geçerli bir tür seçin.',
            'title.required' => 'Başlık zorunludur.',
            'target_amount.required' => 'Hedef tutar zorunludur.',
            'target_amount.numeric' => 'Hedef tutar sayısal olmalı.',
            'target_amount.min' => 'Hedef tutar en az 1 ₺ olmalı.',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $need = Need::findOrFail($id);

        if (! $need->isActive()) {
            session()->flash('message', 'Yalnızca aktif ihtiyaçlar düzenlenebilir.');

            return;
        }

        $this->editingId = $need->id;
        $this->animal_id = (string) $need->animal_id;
        $this->type = $need->type->value;
        $this->title = $need->title;
        $this->description = (string) ($need->description ?? '');
        $this->target_amount = (string) $need->target_amount;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editingId !== null) {
            $need = Need::findOrFail($this->editingId);

            if (! $need->isActive()) {
                session()->flash('message', 'Bu ihtiyaç artık düzenlenemez.');
                $this->cancel();

                return;
            }

            $need->update($data);
            session()->flash('message', 'İhtiyaç güncellendi.');
        } else {
            $data['shelter_id'] = auth()->user()->shelter->id;
            $data['status'] = NeedStatus::Active->value;
            Need::create($data);
            session()->flash('message', 'Yeni ihtiyaç oluşturuldu.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function cancelNeed(int $id): void
    {
        $need = Need::findOrFail($id);

        if ($need->isActive()) {
            $need->update(['status' => NeedStatus::Cancelled->value]);
            session()->flash('message', 'İhtiyaç iptal edildi.');
        }
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'animal_id', 'type', 'title', 'description', 'target_amount']);
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.admin.need-manager', [
            'needs' => Need::with('animal')->latest()->get(),
            'animals' => Animal::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
