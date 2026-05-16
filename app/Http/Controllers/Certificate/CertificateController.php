<?php

declare(strict_types=1);

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Controller;
use App\Models\Donation\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    /**
     * Sertifikayı HTML olarak gösterir (önizleme + indirme).
     */
    public function show(Certificate $certificate): View
    {
        $this->guard($certificate);

        return view('certificates.show', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Sertifikayı PDF olarak indirir.
     */
    public function download(Certificate $certificate): Response
    {
        $this->guard($certificate);

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("sertifika-{$certificate->certificate_no}.pdf");
    }

    /**
     * Banlı kullanıcının belgesi yalnızca kendisine açıktır.
     */
    private function guard(Certificate $certificate): void
    {
        abort_if(
            $certificate->user->is_banned && auth()->id() !== $certificate->user_id,
            404
        );
    }
}
