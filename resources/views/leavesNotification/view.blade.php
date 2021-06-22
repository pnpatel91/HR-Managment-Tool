@component('mail::message')
# Hello {{ $employee_name }},

<p class="text-sm">{{ $text }} <span class="noti-title">{{ $name }}</span></p>

@component('mail::button', ['url' => '{{ $leaveUrl }}'])
View {{ $name }}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent