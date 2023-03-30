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
    <p>Hello..{{ $order->vendor->user->first_name }}</p>
    <table style="width:100%">
        <tr>
            <th>Order Number</th>
            <th>Restaurant Name</th>
            <th>Service Type</th>
            <th>Quantity</th>
        </tr>
        <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->restaurant->name }}</td>
            <td>{{ $order->service->name }}</td>
            <td>{{ $order->quantity }}</td>
        </tr>
    </table>
    <br>
    <a href="{{ route('vendor.approve', $order->id) }}"><button class="button button1">Approve</button></a>
    <a href="{{ route('vendor.reject', $order->id) }}"><button class="button button2">Reject</button></a>
    <p>Thank You</p>
</body>

</html>
