{{--
    Sertifika tasarımı — hem PDF (dompdf) hem HTML önizlemede kullanılır.
    dompdf JavaScript çalıştırmaz ve harici CSS yüklemez; tüm stiller buradadır.
    $certificate değişkeni beklenir. $anonymous true ise bağışçı adı gizlenir.
--}}
@php
    $anonymous = $anonymous ?? false;
    $displayName = $anonymous ? 'Anonim Bağışçı' : $certificate->donor_name;
@endphp

<div class="relife-certificate">
    <div class="rc-border-outer">
        <div class="rc-border-inner">
            <div class="rc-content">

                <div class="rc-brand">Re·Life</div>
                <div class="rc-kicker">Teşekkür Belgesi</div>

                {{-- Pati amblemi (dompdf-uyumlu sade SVG) --}}
                <div class="rc-emblem">
                    <svg width="86" height="86" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                        <g fill="#E8A92B">
                            <ellipse cx="60" cy="78" rx="30" ry="23"/>
                            <ellipse cx="32" cy="50" rx="11" ry="14"/>
                            <ellipse cx="50" cy="34" rx="9" ry="13"/>
                            <ellipse cx="70" cy="34" rx="9" ry="13"/>
                            <ellipse cx="88" cy="50" rx="11" ry="14"/>
                        </g>
                    </svg>
                </div>

                <p class="rc-intro">Bu belge, bir dostun hayatına dokunan</p>

                <div class="rc-donor">{{ $displayName }}</div>

                <p class="rc-body">
                    adına düzenlenmiştir. Yaptığı
                    <strong>₺{{ number_format((float) $certificate->amount, 0, ',', '.') }}</strong>
                    tutarındaki bağış ile
                    @if($certificate->animal_name)
                        <strong>{{ $certificate->animal_name }}</strong> isimli dostun
                        iyileşme yolculuğuna
                    @else
                        bir barınağın ihtiyaçlarına
                    @endif
                    katkıda bulunmuştur.
                </p>

                <p class="rc-thanks">İyiliğin için teşekkür ederiz. 🐾</p>

                <table class="rc-footer">
                    <tr>
                        <td class="rc-foot-left">
                            <div class="rc-foot-label">Belge No</div>
                            <div class="rc-foot-value">{{ $certificate->certificate_no }}</div>
                        </td>
                        <td class="rc-foot-right">
                            <div class="rc-foot-label">Düzenlenme Tarihi</div>
                            <div class="rc-foot-value">{{ $certificate->issued_at->format('d.m.Y') }}</div>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</div>

<style>
    .relife-certificate {
        width: 1000px;
        margin: 0 auto;
        background: #FBF6EA;
        font-family: 'DejaVu Sans', sans-serif;
        color: #1F1B17;
    }
    .relife-certificate .rc-border-outer {
        border: 3px solid #E8A92B;
        padding: 10px;
    }
    .relife-certificate .rc-border-inner {
        border: 1px solid #C7AC74;
        padding: 48px 60px;
        text-align: center;
    }
    .relife-certificate .rc-brand {
        font-size: 26px;
        font-weight: bold;
        color: #586B48;
        letter-spacing: 1px;
    }
    .relife-certificate .rc-kicker {
        margin-top: 6px;
        font-size: 13px;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: #9A6238;
    }
    .relife-certificate .rc-emblem {
        margin: 24px 0 8px 0;
    }
    .relife-certificate .rc-intro {
        font-size: 15px;
        color: #3B342B;
        margin-top: 10px;
    }
    .relife-certificate .rc-donor {
        font-size: 40px;
        font-weight: bold;
        color: #1F1B17;
        margin: 14px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #E8A92B;
        display: inline-block;
    }
    .relife-certificate .rc-body {
        font-size: 15px;
        line-height: 1.7;
        color: #3B342B;
        max-width: 680px;
        margin: 14px auto 0 auto;
    }
    .relife-certificate .rc-body strong {
        color: #586B48;
    }
    .relife-certificate .rc-thanks {
        font-size: 16px;
        color: #9A6238;
        margin-top: 20px;
    }
    .relife-certificate .rc-footer {
        width: 100%;
        margin-top: 44px;
        border-top: 1px solid #E5D6B0;
    }
    .relife-certificate .rc-footer td {
        padding-top: 14px;
        font-size: 12px;
    }
    .relife-certificate .rc-foot-left { text-align: left; }
    .relife-certificate .rc-foot-right { text-align: right; }
    .relife-certificate .rc-foot-label {
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9A6238;
        font-size: 10px;
    }
    .relife-certificate .rc-foot-value {
        margin-top: 3px;
        font-weight: bold;
        color: #1F1B17;
    }
</style>
