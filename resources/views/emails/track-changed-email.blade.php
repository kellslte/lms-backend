@component('mail::message')
# Hi {{ $user->name }},

You recently requested a track change and it has been processed.
Your track has been changed from {{ $course->title }} to {{ $user->course->title }}.

If you did not request this change and would like to reverse it, quickly send a reply to this email and we will attend to you.

Regards,<br>
{{ config('app.name') }}
@endcomponent
