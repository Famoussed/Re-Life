<?php

declare(strict_types=1);

use App\Livewire\Admin\AnimalManager;
use App\Livewire\Admin\AnnouncementManager;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\DonationList as AdminDonationList;
use App\Livewire\Admin\DonorList;
use App\Livewire\Admin\NeedManager;
use App\Livewire\Admin\ShelterProfileEdit;
use App\Livewire\Auth\RegisterShelterAdmin;
use App\Livewire\Donation\DonationFlow;
use App\Livewire\Notification\NotificationCenter;
use App\Livewire\Public\AnimalDetail;
use App\Livewire\Public\AnimalList;
use App\Livewire\Public\Leaderboard;
use App\Livewire\Public\ShelterProfile;
use App\Livewire\Public\UserProfile;
use App\Livewire\Superadmin\BadgeManager;
use App\Livewire\Superadmin\Dashboard as SuperadminDashboard;
use App\Livewire\Superadmin\ShelterApprovals;
use App\Livewire\Superadmin\ShelterList;
use App\Livewire\Superadmin\UserList;
use Illuminate\Support\Facades\Route;

/*
 | Public — giriş gerektirmez
 */
Route::get('/', AnimalList::class)->name('home');
Route::get('/animals/{animal}', AnimalDetail::class)->name('animals.show');
Route::get('/shelters/{shelter}', ShelterProfile::class)->name('shelters.show');
Route::get('/leaderboard', Leaderboard::class)->name('leaderboard');
Route::get('/users/{user}', UserProfile::class)->name('users.show');

/*
 | Misafir — barınak kaydı
 */
Route::get('/admin/register', RegisterShelterAdmin::class)
    ->middleware('guest')
    ->name('admin.register');

/*
 | Donor — giriş gerektirir
 */
Route::middleware('auth')->group(function () {
    Route::get('/donate', DonationFlow::class)->name('donate');
    Route::get('/me/notifications', NotificationCenter::class)->name('notifications');
    Route::view('/profile', 'profile')->name('profile');

    // Giriş sonrası role göre yönlendirme (Breeze 'dashboard' adını kullanır).
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isSuperadmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->isAdmin() && $user->shelter?->isApproved()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    })->name('dashboard');
});

/*
 | Admin paneli — barınak yöneticisi
 */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/animals', AnimalManager::class)->name('animals');
    Route::get('/needs', NeedManager::class)->name('needs');
    Route::get('/donations', AdminDonationList::class)->name('donations');
    Route::get('/donors', DonorList::class)->name('donors');
    Route::get('/announcements', AnnouncementManager::class)->name('announcements');
    Route::get('/shelter', ShelterProfileEdit::class)->name('shelter');
});

/*
 | Superadmin paneli — platform yöneticisi
 */
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', SuperadminDashboard::class)->name('dashboard');
    Route::get('/approvals', ShelterApprovals::class)->name('approvals');
    Route::get('/shelters', ShelterList::class)->name('shelters');
    Route::get('/users', UserList::class)->name('users');
    Route::get('/badges', BadgeManager::class)->name('badges');
});

require __DIR__.'/auth.php';
