<!DOCTYPE html>
<html>
    <head>
        <title>Help Email</title>
    </head>
    <body>
        <h2>A user has sent a help request!</h2>
        <br/>
        <p>User that submitted the request:</p>
        <p><b>{{$user->name()}}</b></p>
        <p><b>{{$user->email}}</b></p>
        <br/>
        <p>Page url with the issue:</p>
        <p><b>{{$page}}</b></p>
        <br/>
        <p>With below comment:</p>
        <p><b>{{$comment}}</b></p>
        <br/>
    </body>
</html>