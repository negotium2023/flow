<!DOCTYPE html>
<html>
    <head>
        <title>Trial Notification</title>
    </head>
    <body>
        <div class="fr-view">
            <p>Dear {{$user->last_name}}.</p>
            <p>You have {{$days_left}} {{($days_left == 1)?'day':'days'}} left on your trial.</p>
            <p>You can upgrade to full package <a href="https://helpdesk.blackboardbs.com">here</a></p>
        </div>
    </body>
</html>