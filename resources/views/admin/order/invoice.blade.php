<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="" type="image/x-icon">       
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
        .content {
            border: 3px solid #000;
            height: 100em !important;
        }
        img#invoice {
            width: 60% !important;
        }

        img#footer {
            width: 40% !important;
        }

        table#header {
            width: 100% !important;
        }

        table#header, table#header tr, table#header td {
            border: 1px solid #000 !important;
            color: #000 !important;
        }

        table#header td:nth-child(1) {
            text-align: left !important;
            background-color: rgb(217,217,217) !important;
            padding-left: 10px !important;
        }

        table#header td:nth-child(2) {
            text-align: right !important;
            padding-right: 10px !important;
        }
        table#content {
            margin-top: 125px !important;
            width: 100% !important;
        }

        table#content tr, table#content th, table#content td {
            border: 1px solid #000 !important;
            text-align: center !important;
        }

        table#content tr td {
            color: #000 !important;
        }

        table#content thead {
            background-color: #000 !important;
            padding: 3px !important;
        }

        table#content thead tr th {
            color: #fff !important;
        }

        table#content tfoot tr:nth-child(-n+4), table#content tfoot td:nth-child(-n+3) {
            border: none !important;
        }

        table#content tfoot td:nth-child(4) {
            background-color: rgb(217, 217, 217) !important;
            text-align: left !important;
            padding-left: 10px !important;
        }
        table#content tfoot td:nth-child(4) b {
            text-align: left !important;
            padding-left: 10px !important;
        }
        .foot {
            border-top: 6px solid #f8b700 !important;
            background-color: #000 !important;
            position: absolute !important;
            bottom: 0 !important;
            left: 18px !important;
            right: 18px !important;
            padding: 20px 10px;
        }

        .foot p {
            font-size: 25px !important;
            color: #fff !important;
            text-align: right !important;
            font-weight: bold !important;
        }
        p.question {
            color: #f8b700 !important;
            padding-right: 15px !important; 
        }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row border">
                <div class="col-xs-12">
                    <div class="clearfix"></div>
                    <div class="content" id="myDiv">
                        <div class="data-table table-responsive">
                            <div class="row">
                                <div class="col-xs-4">
                                    <img src="http://app.toolbx.com/images/toolboxlogo_temp.png" alt="ToolBX Logo" id="invoice" width="60mm">
                                </div>
                                <div class="col-xs-6 col-xs-offset-2">
                                    <table id="header">
                                        <tbody>
                                            <tr>
                                                <td><b>Company Name</b></td>
                                                <td>{{ $order->CompanyName }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Order Number</b></td>
                                                <td>TB-{{ (181110 + $order->OrderId) }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Jobsite</b></td>
                                                <td>{{ $order->JobSiteName }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Ordered By</b></td>
                                                <td>{{ $order->RegistrationName }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Transaction Date</b></td>
                                                <td>{{ date('M d, Y', strtotime($order->OrderDate)) }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Status</b></td>
                                                <td>{{ $order->status }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <table id="content">
                                        <thead>
                                            <tr>
                                                <th>Manufacturer</th>
                                                <th>Product Name</th>
                                                <th>SKU</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($order_details as $detail)
                                                <tr>
                                                    <td>{{ $detail->manufacturer }}</td>
                                                    <td>{{ $detail->ProductName }}</td>
                                                    <td>{{ $detail->sku }}</td>
                                                    <td>{{ $detail->Quantity }}</td>
                                                    <td>${{ number_format($detail->Rate + $detail->Rate * 0.1, 2) }}</td>
                                                    <td>${{ number_format(($detail->Rate + $detail->Rate * 0.1) * $detail->Quantity, 2) }}</td>
                                                </tr>
                                            @empty
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="left-align" colspan="2"><b>Subtotal</b></td>
                                                <td>${{ number_format($order->SubTotal + ($order->SubTotal * 0.1), 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="left-align" colspan="2"><b>HST (13%)</b></td>
                                                <td>${{ number_format(  $order->TaxAmount , 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="left-align" colspan="2"><b>Shipping</b></td>
                                                <td>${{ $order->DeliveryCharges }}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="left-align" colspan="2"><b>Total</b></td>
                                                <td>${{ number_format( ( $order->SubTotal + ($order->SubTotal * 0.1) + $order->TaxAmount + $order->DeliveryCharges ), 2)  }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div> 
                        <div class="foot">
                            <div class="row">
                                <div class="col-xs-6">
                                    <img src="{{ asset('images/281f09da-e832-48a1-ba0d-a2bdb9fa1977.png') }}" id="footer">
                                </div>
                                <div class="col-xs-6">
                                    <p class="question">Questions?</p>
                                    <p>billing@toolbx.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
