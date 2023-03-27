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
    <table style="width:100%">
        <tr>
            <th>Order Id</th>
            <th>Restaurant Name</th>
            <th>Vendor Name</th>
            <th>Stock Type</th>
            <th>Total Amount</th>
            <th>Tax</th>
            <th>Due Date</th>


        </tr>
        <tr>
            <td>{{ $bill->order_id }}</td>
            <td>{{ $bill->restaurant->name }}</td>
            <td>{{ $bill->vendor->user->first_name }}</td>
            <td>{{ $bill->stock->name }}</td>
            <td>{{ $bill->total_amount }}</td>
            <td>{{ $bill->tax }}</td>
            <td>{{ $bill->due_date }}</td>

        </tr>
    </table>
    <p>Thank You</p>
</body>

</html>
