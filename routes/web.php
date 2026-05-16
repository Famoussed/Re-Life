<?php

declare(strict_types=1);

use App\Livewire\Account\UserList;
use App\Livewire\Account\UserProfile;
use App\Livewire\Animal\AnimalDetail;
use App\Livewire\Animal\AnimalList;
use App\Livewire\Animal\AnimalManager;
use App\Livewire\Animal\NeedManager;
use App\Livewire\Animal\RecoveryUpdateManager;
use App\Livewire\Donation\BadgeManager;
use App\Livewire\Donation\DonationFlow;
use App\Livewire\Donation\DonationList;
use App\Livewire\Donation\DonorList;
use App\Livewire\Donation\Leaderboard;
use App\Livewire\Notification\NotificationCenter;
use App\Livewire\Shelter\AdminDashboard;
use App\Livewire\Shelter\AnnouncementManager;
use App\Livewire\Shelter\RegisterShelterAdmin;
use App\Livewire\Shelter\ShelterApprovals;
use App\Livewire\Shelter\ShelterList;
use App\Livewire\Shelter\ShelterProfile;
use App\Livewire\Shelter\ShelterProfileEdit;
use App\Livewire\Shelter\SuperadminDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    // Çıkış (Breeze Livewire stack'inde hazır route yok — burada tanımlanır).
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');

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
    Route::get('/recovery-updates', RecoveryUpdateManager::class)->name('recovery-updates');
    Route::get('/donations', DonationList::class)->name('donations');
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
