@props(['vulnerabilities'])

<x-mail::message>
<x-mail::table>
| Package | Via | Severity | Range |
| :--- | :---: | ---: | ---: |
@foreach($vulnerabilities as $name => $meta)
    @foreach($meta['via'] as $vulnerability)
           @isset($vulnerability['title'])
                | [{{ $name }}]({{ 'https://www.npmjs.com/package/' . $name  }}) | @isset($vulnerability['url']) [{{ $vulnerability['title'] }}]({{ $vulnerability['url'] }}) @else {{ $vulnerability['title'] }} @endisset | {{ $meta['severity'] ?? 'N/A' }} | {{ str_replace('||', 'and', $meta['range']) ?? 'N/A' }} |
           @endisset
    @endforeach
@endforeach
</x-mail::table>
</x-mail::message>
