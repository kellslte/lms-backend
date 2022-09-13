@component('mail::message')
# Hello, I am {{ $report->reporter->name }},

My student ID is {{ $report->reporter->id }}, and my track is {{ $report->reporter->course->title }}.

My email address is {{ $report->reporter->email }}.

{{ $report->details }}

Thanks,<br>
@endcomponent
