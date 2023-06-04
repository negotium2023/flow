<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>

<body>
<h2>Welcome to the {{env('APP_NAME')}} site {{$name}}</h2>
<br/>
Your registered email-id is {{$email}} , Please click <a href="{{url('user/verify',$token)}}">here</a> to verify your email account and gain access to the system.
<br/>
<br/>
You will however only have full access to the system once an Administrative User has assigned you to a role.
<br />
<br />
Kind Regards<br />
The UHY Team
</body>

</html>