<!DOCTYPE html>
<html>
<head>
    <title>{{$name}} has been assigned to you</title>
</head>

<h2>Please Note</h2>

This email is to inform you that {{$name}} has been assigned to you.<br />
<br />
Click <a href="{{env('APP_URL')}}/clients/show/{{$id}}">here</a> to view client.

</body>

</html>