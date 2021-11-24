@component('mail::message')
# Hello {{ $receiver_name }},

<p class="text-sm">{{ $text }} - From ({{ $employee_name }})</p>

@component('mail::button', ['url' => $actionUrl])
View Leave
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent