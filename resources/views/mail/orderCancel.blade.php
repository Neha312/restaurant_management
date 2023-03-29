<!DOCTYPE html>
<html lang="en">

<head>
    <title>Order cancale Mail</title>
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
    <p>Hello..{{ $order->restaurant->users->first()->first_name }}</p>
    <p>Order cancale by {{ $order->vendor->user->first_name }}</p>
    <table style="width:100%">
        <tr>
            <th>Order Id</th>
            <th>Restaurant Name</th>
            <th>Service Type</th>
            <th>Quantity</th>

        </tr>
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->restaurant->name }}</td>
            <td>{{ $order->service->name }}</td>
            <td>{{ $order->quantity }}</td>
        </tr>
    </table>
    <p>Thank You</p>
</body>

</html>
