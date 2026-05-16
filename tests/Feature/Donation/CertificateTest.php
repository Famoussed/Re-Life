<?php

declare(strict_types=1);

namespace Tests\Feature\Donation;

use App\Actions\Donation\CreateCertificateAction;
use App\Enums\Account\Role;
use App\Enums\Shelter\ShelterStatus;
use App\Models\Account\User;
use App\Models\Animal\Animal;
use App\Models\Animal\Need;
use App\Models\Donation\Certificate;
use App\Models\Shelter\Shelter;
use App\Services\Donation\DonationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    private function makeShelter(string $email, string $license): Shelter
    {
        $admin = User::create([
            'name' => 'Yönetici '.$email,
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => Role::Admin->value,
            'email_verified_at' => now(),
        ]);

        return Shelter::create([
            'admin_user_id' => $admin->id,
            'name' => 'Barınak '.$license,
            'license_no' => $license,
            'city' => 'İstanbul',
            'phone' => '0500 000 0000',
            'address' => 'Test adresi',
            'status' => ShelterStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }

    private function makeAnimal(Shelter $shelter, string $name = 'Pati'): Animal
    {
        return Animal::withoutGlobalScopes()->create([
            'shelter_id' => $shelter->id,
            'name' => $name,
            'species' => 'dog',
            'age_estimate' => '2 yaş',
            'gender' => 'male',
            'story' => 'Test hikâyesi.',
            'health_status' => 'İyileşiyor.',
            'is_active' => true,
        ]);
    }

    private function makeActiveNeed(Animal $animal, float $target = 1000): Need
    {
        return Need::withoutGlobalScopes()->create([
            'animal_id' => $animal->id,
            'shelter_id' => $animal->shelter_id,
            'type' => 'food',
            'title' => 'Mama desteği',
            'target_amount' => $target,
            'status' => 'active',
        ]);
    }

    private function makeDonor(string $email): User
    {
        return User::create([
            'name' => 'Bağışçı '.$email,
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => Role::User->value,
            'email_verified_at' => now(),
        ]);
    }

    public function test_donation_creates_certificate_via_listener(): void
    {
        $shelter = $this->makeShelter('admin1@test.com', 'LIC-001');
        $animal = $this->makeAnimal($shelter, 'Boncuk');
        $need = $this->makeActiveNeed($animal);
        $donor = $this->makeDonor('donor1@test.com');

        $donation = app(DonationService::class)->create($donor, [
            'need_id' => $need->id,
            'amount' => 250,
            'card_number' => '4242424242424242',
            'card_holder' => $donor->name,
        ]);

        $this->assertDatabaseHas('certificates', [
            'donation_id' => $donation->id,
            'user_id' => $donor->id,
            'donor_name' => $donor->name,
            'animal_name' => 'Boncuk',
            'amount' => '250.00',
        ]);
    }

    public function test_certificate_number_has_expected_format(): void
    {
        $shelter = $this->makeShelter('admin2@test.com', 'LIC-002');
        $animal = $this->makeAnimal($shelter);
        $need = $this->makeActiveNeed($animal);
        $donor = $this->makeDonor('donor2@test.com');

        $donation = app(DonationService::class)->create($donor, [
            'need_id' => $need->id,
            'amount' => 100,
            'card_number' => '4242424242424242',
            'card_holder' => $donor->name,
        ]);

        $certificate = Certificate::where('donation_id', $donation->id)->firstOrFail();

        $this->assertMatchesRegularExpression('/^RL-\d{4}-\d{6}$/', $certificate->certificate_no);
    }

    public function test_certificate_creation_is_idempotent(): void
    {
        $shelter = $this->makeShelter('admin3@test.com', 'LIC-003');
        $animal = $this->makeAnimal($shelter);
        $need = $this->makeActiveNeed($animal);
        $donor = $this->makeDonor('donor3@test.com');

        $donation = app(DonationService::class)->create($donor, [
            'need_id' => $need->id,
            'amount' => 100,
            'card_number' => '4242424242424242',
            'card_holder' => $donor->name,
        ]);

        // Action'ı tekrar çalıştır — ikinci sertifika oluşmamalı.
        app(CreateCertificateAction::class)->execute($donation);

        $this->assertSame(1, Certificate::where('donation_id', $donation->id)->count());
    }

    public function test_cover_all_donations_each_get_a_certificate(): void
    {
        $shelter = $this->makeShelter('admin4@test.com', 'LIC-004');
        $animal = $this->makeAnimal($shelter);
        $this->makeActiveNeed($animal, 500);
        $this->makeActiveNeed($animal, 800);
        $donor = $this->makeDonor('donor4@test.com');

        app(DonationService::class)->coverAllNeeds($donor, $animal, [
            'is_anonymous' => false,
            'card_number' => '4242424242424242',
            'card_holder' => $donor->name,
        ]);

        // İki aktif ihtiyaç → iki bağış → iki sertifika.
        $this->assertSame(2, Certificate::where('user_id', $donor->id)->count());
    }

    public function test_certificate_pdf_can_be_downloaded(): void
    {
        $shelter = $this->makeShelter('admin5@test.com', 'LIC-005');
        $animal = $this->makeAnimal($shelter);
        $need = $this->makeActiveNeed($animal);
        $donor = $this->makeDonor('donor5@test.com');

        $donation = app(DonationService::class)->create($donor, [
            'need_id' => $need->id,
            'amount' => 100,
            'card_number' => '4242424242424242',
            'card_holder' => $donor->name,
        ]);

        $certificate = Certificate::where('donation_id', $donation->id)->firstOrFail();

        $response = $this->actingAs($donor)->get(route('certificates.pdf', $certificate));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_certificate_show_page_displays_data(): void
    {
        $shelter = $this->makeShelter('admin6@test.com', 'LIC-006');
        $animal = $this->makeAnimal($shelter, 'Zeytin');
        $need = $this->makeActiveNeed($animal);
        $donor = $this->makeDonor('donor6@test.com');

        $donation = app(DonationService::class)->create($donor, [
            'need_id' => $need->id,
            'amount' => 300,
            'card_number' => '4242424242424242',
            'card_holder' => $donor->name,
        ]);

        $certificate = Certificate::where('donation_id', $donation->id)->firstOrFail();

        $response = $this->actingAs($donor)->get(route('certificates.show', $certificate));

        $response->assertOk();
        $response->assertSee($certificate->certificate_no);
        $response->assertSee($donor->name);
        $response->assertSee('Zeytin');
    }
}
