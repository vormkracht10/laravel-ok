@props(['vulnerabilities'])

<x-mail::message>
<x-mail::table>
| Package | Affected Versions |
| :--- | ---: |
@foreach($vulnerabilities as $package => $advisories)
    @foreach($advisories as $advisory)
        | [{{ $package }}]({{ $advisory['link'] ?? 'N/A' }})<br>{{ $advisory['title'] ?? 'N/A' }} | {{ $advisory['affectedVersions'] ?? 'N/A' }} |
    @endforeach
@endforeach
</x-mail::table>
</x-mail::message>
