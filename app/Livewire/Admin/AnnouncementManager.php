<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Duyurular — Re·Life')]
class AnnouncementManager extends Component
{
    public bool $showForm = false;

    public string $title = '';

    public string $body = '';

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'title.required' => 'Duyuru başlığı zorunludur.',
            'body.required' => 'Duyuru içeriği zorunludur.',
        ];
    }

    public function create(): void
    {
        $this->reset(['title', 'body']);
        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(AnnouncementService $service): void
    {
        $data = $this->validate();

        $service->publish(auth()->user()->shelter, [
            'title' => $data['title'],
            'body' => $data['body'],
        ]);

        session()->flash('message', 'Duyuru yayınlandı ve bağışçılara bildirim gönderildi.');

        $this->reset(['title', 'body']);
        $this->showForm = false;
    }

    public function cancel(): void
    {
        $this->reset(['title', 'body']);
        $this->resetValidation();
        $this->showForm = false;
    }

    public function render(): View
    {
        $shelter = auth()->user()->shelter;

        return view('livewire.admin.announcement-manager', [
            'announcements' => Announcement::where('shelter_id', $shelter->id)
                ->latest()
                ->get(),
        ]);
    }
}
