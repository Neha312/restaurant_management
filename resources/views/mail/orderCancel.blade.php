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
    <p>Hello..{{ $user->first_name }}</p>
    <p>Order cancel by {{ $order_item->vendor->user->first_name }}</p>
    <table style="width:100%">
        <tr>
            <th>Order Number</th>
            <th>Restaurant Name</th>
            <th>Service Type</th>
            <th>Quantity</th>
        </tr>
        <tr>
            <td>{{ $order_item->order->order_number }}</td>
            <td>{{ $order_item->restaurant->name }}</td>
            <td>{{ $order_item->service->name }}</td>
            <td>{{ $order_item->quantity }}</td>
        </tr>
    </table>
    <p>Thank You</p>
</body>

</html>
