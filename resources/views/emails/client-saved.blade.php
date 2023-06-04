@component('mail::message')
# Introduction

The client saved.

@component('mail::button', ['url' => route('clients.overview', [$client,$process_id,$step_id])])
Viev Client
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
