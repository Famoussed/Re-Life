<?php

declare(strict_types=1);

namespace App\Events\Donation;

use App\Models\Donation\Donation;
use Illuminate\Foundation\Events\Dispatchable;

class DonationCreated
{
    use Dispatchable;

    public function __construct(public Donation $donation)
    {
    }
}
