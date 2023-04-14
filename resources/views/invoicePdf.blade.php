<!DOCTYPE html>
<html>

<head>
    <title>Bill PDF Download</title>
</head>
<style type="text/css">
    body {
        font-family: 'Roboto Condensed', sans-serif;
    }

    .m-0 {
        margin: 0px;
    }

    .p-0 {
        padding: 0px;
    }

    .pt-5 {
        padding-top: 5px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .text-center {
        text-align: center !important;
    }

    .w-100 {
        width: 100%;
    }

    .w-50 {
        width: 50%;
    }

    .w-85 {
        width: 85%;
    }

    .w-15 {
        width: 15%;
    }

    .logo img {
        width: 200px;
        height: 60px;
    }

    .gray-color {
        color: #5D5D5D;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid rgb(14, 11, 11);
    }

    table tr,
    th,
    td {
        border: 1px solid #cd9c9c;
        border-collapse: collapse;
        padding: 7px 8px;
    }

    table tr th {
        background: #8aadcf;
        font-size: 15px;
    }

    table tr td {
        font-size: 13px;
    }

    table {
        border-collapse: collapse;
    }

    .box-text p {
        line-height: 10px;
    }

    .float-left {
        float: left;
    }

    .total-part {
        font-size: 16px;
        line-height: 12px;
    }

    .total-right p {
        padding-right: 20px;
    }
</style>

<body>
    <div class="head-title">
        <h1 class="text-center m-0 p-0">Invoice</h1>
    </div>
    <div class="add-detail mt-10">
        <div class="w-50 float-left mt-10">
            <p class="m-0 pt-5 text-bold w-100">Bill Number - <span class="gray-color">{{ $bill->bill_number }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Order Number - <span
                    class="gray-color">{{ $bill->order->order_number }}</span>
            </p>
            <p class="m-0 pt-5 text-bold w-100">Order Date - <span
                    class="gray-color">{{ $bill->order->created_at }}</span></p>
            <p class="m-0 pt-5 text-bold w-100">Restaurant Name - <span
                    class="gray-color">{{ $bill->restaurant->name }}</span>
            </p>
        </div>
        <div class="w-50 float-left logo mt-10">
            <img src="../public/invoice.png" alt="Logo">
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">From</th>
                <th class="w-50">To</th>
            </tr>
            <tr>
                <td>
                    <div class="box-text">
                        <p>Name:{{ $order_item->vendor->user->first_name . ' ' . $order_item->vendor->user->last_name }}
                        </p>
                        <p>Address:{{ $order_item->vendor->user->address1 }}</p>
                        <p>Email:{{ $order_item->vendor->user->email }}</p>
                        <p>Contact:{{ $order_item->vendor->user->phone }}</p>
                    </div>
                </td>
                <td>
                    <div class="box-text">
                        <p>Name:{{ $user->first_name . ' ' . $user->last_name }}</p>
                        <p>Address:{{ $user->address1 }}</p>
                        <p>Email:{{ $user->email }}</p>
                        <p>Contact:{{ $user->phone }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-section bill-tbl w-100 mt-10">
        <table class="table w-100 mt-10">
            <tr>
                <th class="w-50">Stock Name</th>
                <th class="w-50">Price</th>
                <th class="w-50">Qty</th>
                <th class="w-50">Tax</th>
                <th class="w-50">Total Amount</th>
            </tr>
            <tr align="center">
                <td>{{ $order_item->stock->name }}</td>
                <td>{{ '$' . $order_item->stock->price }}</td>
                <td>{{ $order_item->quantity }}</td>
                <td>{{ $bill->tax . '%' }}</td>
                <td>{{ '$' . $bill->total_amount }}</td>
            </tr>
            <td colspan="7">
                <div class="total-part">
                    <div class="total-left w-85 float-left" align="right">
                        <p>Total:</p>
                        <p>Tax:</p>
                        <p>Total Payable:</p>
                        <p>Due Date:</p>
                    </div>
                    <div class="total-right w-15 float-left text-bold" align="right">
                        <p>{{ '$' . $bill->total_amount }}</p>
                        <p>{{ $bill->tax . '%' }}</p>
                        <p>{{ '$' . $bill->total_amount }}</p>
                        <p>{{ $bill->due_date }}</p>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </td>
            </tr>
        </table>
        <h3 align="center">Thankyou for Shopping</h3>
    </div>

</html>
