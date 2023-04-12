<!DOCTYPE html>
<html lang="en">

<head>
    <title>Order Detail Mail</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }

        th,
        td {
            border: 1px solid black;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #D6EEEE;
        }
    </style>
</head>

<body>

    <p>Hello..{{ $vendor->user->first_name }}</p>
    <table style="width:100%">
        <tr>
            <th>Vendor Id</th>
            <th>Vendor Name</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>{{ $vendor->id }}</td>
            <td>{{ $vendor->user->first_name }}</td>
            <td>{{ $status }}</td>
        </tr>
    </table>
    <p>Thank You</p>
</body>

</html>
