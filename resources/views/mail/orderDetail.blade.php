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

    <h3>Hello..{{ $user->first_name }}</h3>
    <h3>Order Number:{{ $order_create->order_number }}</h3>
    <table style="width:100%">
        <tr>
            <th>Restaurant Name</th>
            <th>Service Type</th>
            <th>Quantity</th>
        </tr>
        <tr>
            <td>{{ $item->restaurant->name }}</td>
            <td>{{ $item->service->name }}</td>
            <td>{{ $item->quantity }}</td>
        </tr>
    </table>
    @php
        $tax = ($item->price * $item->stock->tax) / 100;
        $total_amount = ($item->price + $tax) * $item->quantity;
    @endphp
    <h3 align="right">Total Amount:{{ $total_amount }}</h3>
    <br>
    <a href="{{ route('vendor.approve', $item->id) }}"><button class="button button1">Approve</button></a>
    <a href="{{ route('vendor.reject', $item->id) }}"><button class="button button2">Reject</button></a>
    <p>Thank You</p>
</body>

</html>
