<!DOCTYPE html>
<html>
    <head>
        <title>Welcome Email</title>
    </head>
    <body>
        <h2>Welcome to the {{env('APP_NAME')}} site {{$user->first_name}}</h2>
        <br/>
        Your registered email-id is {{$user->email}}
        <br/><br/>
        Your password is {{$password}}
        <br/><br/>
        Please click on the link below to login and change your password.
        <br/><br/>
        <a href="{{ url('/settings') }}" target="_blank">{{ url('/settings') }}</a>
    </body>
</html>