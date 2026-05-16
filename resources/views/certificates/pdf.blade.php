<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Re·Life — Teşekkür Belgesi {{ $certificate->certificate_no }}</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 20px; background: #FBF6EA; }
    </style>
</head>
<body>
    @include('certificates.partials.design', ['certificate' => $certificate])
</body>
</html>
