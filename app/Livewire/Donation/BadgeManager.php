<?php

declare(strict_types=1);

namespace App\Livewire\Donation;

use App\Models\Donation\Badge;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.panel')]
#[Title('Rozetler — Re·Life')]
class BadgeManager extends Component
{
    /**
     * @var array<int, array{name: string, min_amount: string}>
     */
    public array $badges = [];

    public function mount(): void
    {
        $this->loadBadges();
    }

    public function loadBadges(): void
    {
        $this->badges = Badge::orderBy('level')
            ->get()
            ->mapWithKeys(fn (Badge $badge) => [
                $badge->id => [
                    'name' => (string) $badge->name,
                    'min_amount' => (string) $badge->min_amount,
                ],
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        $rules = [];

        foreach (array_keys($this->badges) as $id) {
            $rules["badges.{$id}.name"] = 'required|string|max:255';
            $rules["badges.{$id}.min_amount"] = 'required|numeric|min:0';
        }

        return $rules;
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->badges as $id => $data) {
            Badge::where('id', $id)->update([
                'name' => $data['name'],
                'min_amount' => $data['min_amount'],
            ]);
        }

        $this->loadBadges();

        session()->flash('status', 'Rozet tanımları kaydedildi.');
    }

    public function render(): View
    {
        return view('livewire.donation.badge-manager', [
            'badgeModels' => Badge::orderBy('level')->get(),
        ]);
    }
}
