<?php

declare(strict_types=1);

namespace App\Livewire\Animal;

use App\Models\Animal\Animal;
use App\Models\Animal\RecoveryUpdate;
use App\Services\Animal\RecoveryService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.panel')]
#[Title('İyileşme Güncellemeleri — Re·Life')]
class RecoveryUpdateManager extends Component
{
    use WithFileUploads;

    public bool $showForm = false;

    public ?int $animalId = null;

    public string $title = '';

    public string $note = '';

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $photos = [];

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'animalId' => ['required', 'integer', 'exists:animals,id'],
            'title' => ['required', 'string', 'max:150'],
            'note' => ['required', 'string', 'max:5000'],
            'photos' => ['required', 'array', 'min:1', 'max:6'],
            'photos.*' => ['image', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'animalId.required' => 'Lütfen bir hayvan seçin.',
            'title.required' => 'Güncelleme başlığı zorunludur.',
            'note.required' => 'İyileşme notu zorunludur.',
            'photos.required' => 'En az bir fotoğraf yükleyin.',
            'photos.min' => 'En az bir fotoğraf yükleyin.',
            'photos.max' => 'En fazla 6 fotoğraf yükleyebilirsiniz.',
            'photos.*.image' => 'Yüklenen dosyalar görsel olmalıdır.',
            'photos.*.max' => 'Her fotoğraf en fazla 4 MB olabilir.',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save(RecoveryService $service): void
    {
        $data = $this->validate();

        // ShelterScope sayesinde başka barınağın hayvanı bulunamaz.
        $animal = Animal::findOrFail($data['animalId']);

        $service->publish($animal, [
            'title' => $data['title'],
            'note' => $data['note'],
        ], $this->photos);

        session()->flash('message', 'İyileşme güncellemesi yayınlandı ve bağışçılara bildirim gönderildi.');

        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->reset(['animalId', 'title', 'note', 'photos']);
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.animal.recovery-update-manager', [
            'animals' => Animal::orderBy('name')->get(),
            'updates' => RecoveryUpdate::with(['animal', 'images'])->latest()->get(),
        ]);
    }
}
