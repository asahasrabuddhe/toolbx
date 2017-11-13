<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-md-offset-1 col-sm-10">
                <div class="clearfix"></div>
                <div class="content" id="myDiv">
                    <div class="data-table table-responsive">
                        <div class="row">
                            <div class="col-sm-3">
                                <label>ORDER NUMBER </label><span> {{ $order_details->OrderId }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label>JOB SITE ADDRESS </label><span> {{ $order_details->Address or '-' }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label>DATE PURCHASED </label><span> {{ date('m/d/y', strtotime($order_details->OrderDate)) }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label>DELIVERY STATUS </label><span> {{ $order_details->status or '-' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <label>TOTAL AMOUNNT </label><span> ${{ $order_details->TotalAmount }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label>HST </label><span> ${{ $order_details->TaxAmount }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label>DELIVERY FEE </label><span> ${{ $order_details->DeliveryCharges }}</span>
                            </div>
                            <div class="col-sm-3">
                                <label>TOTAL </label><span> ${{ $order_details->TotalAmount + $order_details->TaxAmount + $order_details->DeliveryCharges }}</span>
                            </div>
                        </div>
                        <table class="table" id="users">
                            <thead>      
                                <tr>
                                    <th class="clsleftheader">ORDER DETAILS</th>
                                    <th class="clsheader">QUANTITY</th>
                                    <th class="clsrightheader">AMOUNT IN $</th>
                                </tr>
                            </thead>            
                            <tbody>
                                @forelse($order_details_table as $row)
                                    <tr>
                                        <td>{{ $row->ProductName }}</td>
                                        <td>{{ $row->Quantity }}</td>
                                        <td>{{ $row->Amount }}</td>
                                    </tr>
                                @empty
                                    <tr>No Data Available</tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('/js/app.js') }}"></script>
    </body>
</html>
