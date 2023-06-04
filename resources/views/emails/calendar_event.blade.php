<!DOCTYPE html>
<html>
    <head>
        <title>Calendar Event</title>
    </head>
    <body>
        <p>Dear,<p>
        <p>Click on the link below to accept appointment.</p>
        <p>Date: {{$date}}</p>
        <p><a href="{{route('calendarevents.accept', $id)}}" target="_blank">Accept</a> | <a href="{{route('calendarevents.reject', $id)}}" target="_blank">Reject</a></p>
    </body>
</html>