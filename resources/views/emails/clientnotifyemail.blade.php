<!DOCTYPE html>
<html>
<head>
    <title>Client Registration Email</title>
</head>

    <h2>New client registration</h2>

    This email is to inform you that {{$name}} was registered as a new client on the {{env('APP_NAME')}} site.
    <br />
    <br />
    To approve this client please click <a href="{{url('/clients/show/'.$link)}}">here</a>
    </body>

</html>