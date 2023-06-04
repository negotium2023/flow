<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
<strong>Dear Client,</strong>

<p>This message is sent on behalf of UHY Farrelly Dawe White Limited in relation to our KYC procedural requirements.<br />
    Please see a link below which will allow you to provide us with some outstanding information that we require.</p>
    

<table border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td><strong>Link:</strong></td>
        <td><a href="{{url('/client/'.$clientid.'/progress/'.$process_id.'/'.$step_id)}}">{{url('/client/'.$clientid.'/progress/'.$process_id.'/'.$step_id)}}</a></td>
    </tr>
    <tr>
        <td><strong>Username:</strong></td>
        <td>{{$email}}</td>
    </tr>
    <tr>
        <td><strong>Password: </strong></td>
        <td>{{$password}}</td>
    </tr>
</table>

<p>Should you have any issues or questions, please do not hesitate to contact us.</p>

</body>

</html>