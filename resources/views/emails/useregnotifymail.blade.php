<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>

<h2>New user registration</h2>

This email is to inform you of a new user registration on the {{env('APP_NAME')}} site from {{$name}} {{$surname}} with email address {{$email}}.
<br />
<br />
To assign this user to a role please click <a href="{{url('/users/'.$link.'/edit')}}">here</a>
</body>

</html>