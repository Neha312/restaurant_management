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
    <p>Hello..{{ $bill->restaurant->users->first()->first_name }}</p>
    <p>Bill Details</p>
    <table style="width:100%">
        <tr>
            <th>Bill Number</th>
            <th>Order Number</th>
            <th>Restaurant Name</th>
            <th>Vendor Name</th>
            <th>Stock Type</th>
            <th>Quantity</th>
            <th>Tax</th>
            <th>Due Date</th>


        </tr>
        <tr>
            <td>{{ $bill->bill_number }}</td>
            <td>{{ $bill->order->order_number }}</td>
            <td>{{ $bill->restaurant->name }}</td>
            <td>{{ $bill->vendor->user->first_name }}</td>
            <td>{{ $bill->stock->name }}</td>
            <td>{{ $bill->order->orderItem->first()->quantity }}</td>
            <td>{{ $bill->tax }}</td>
            <td>{{ $bill->due_date }}</td>
        </tr>
    </table>
    <h3 align="right" margin-right=30px>Total Amount:{{ $bill->total_amount }}</h3>
    <a href="{{ route('bill.download', $bill->id) }}"><button class="button button1">Download</button></a>
    <p>Thank You</p>

</body>

</html>
