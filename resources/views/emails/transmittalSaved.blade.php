<!DOCTYPE html>
<html>
<head>
    <style>
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
        }
    </style>
</head>
<body>
    <p>Good day! Below are the updated details of the Transmittal No. <b>{{ $transmittalno }}</b>:</p>
    <br>
    <p>Purpose: <b>{{ $purpose }}</b></p>
    <p>Type: <b>{{ $transType }}</b></p>
    <p>Date Needed: <b>{{ $date_needed }}</b></p>
    <p>Priority: <b>{{ $priority }}</b></p>
    <p>Source: <b>{{ $source }}</b></p>
    <p>Email Address: <b>{{ $email_address }}</b></p>
    <br>
    <p>The transmittal items: </p>
    <br>
    <table style="width: 100%">
        <tr>
            <th>Sample No.</th>
            <th>Desciption</th>
            <th>Elements</th>
            <th>Method Code</th>
            <th>Comments</th>
        </tr>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->sampleno }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->elements }}</td>
                <td>{{ $item->methodcode }}</td>
                <td>{{ $item->comments }}</td>
            </tr>
        @endforeach
    </table>
    <br>
    <p>Thank you.</p>
</body>
</html>