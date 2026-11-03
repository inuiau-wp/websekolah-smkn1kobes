{{-- Favicon mengikuti logo sekolah yang diunggah (SchoolProfile) --}}
@php($faviconUrl = $schoolProfile->logo_url ?? null)
@if ($faviconUrl)
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
@else
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-sekolah.svg') }}">
@endif
