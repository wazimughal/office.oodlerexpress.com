<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Customer Invoice</title>
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        body {
            position: relative;
            width: 19cm;
            height: 27.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-family: Arial;
        }

        header {
            padding: 10px 0;
            margin-bottom: 30px;
        }

        #logo {
            text-align: center;
            margin-bottom: 10px;
            background: #5D6975;
        }

        #logo img {
            xwidth: 260px;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #5D6975;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 20px 0;
            background: url(dimension.png);
        }

        #project {
            float: left;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }
        
        table td {
            padding: 5px;
            /* text-align: right; */
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }
      
        table td.unit,
        table td.qty,
        table td.total {
            font-size: 12px;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }
        .service{width:10%; text-align: center; font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
        .qty{width: 14%; text-align: center;font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
        .desc{width: 39%; text-align: center;font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
        .total{width: 5%; text-align: center;font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
    </style>
</head>

<body>
    @php
        
        $overtime =12;
      //$logo_url='https://office.oodlerexpress.com/adminpanel/dist/img/2.png';
      //$logo_url='http://127.0.0.1:8000/adminpanel/dist/img/2.png';
      $logo_url=storage_path('app/public/2.png');
    @endphp

    <header class="clearfix">
        
        <div id="logo" style="width: 200px; margin:0 auto; text-align:center;">
            {{-- <img src="{{ url('adminpanel/dist/img/oodler-Final-logo-white.png') }}" alt="OodlerExpress CRM" width="100%"> --}}
            {{-- <img src="{{$logo_url}}" alt="{{$logo_url}} CRM" width="50%"> --}}
        </div>
        
        
        
        {{-- <h1>INVOICE:{{ ((!empty($delivery['invoices']))?$delivery['invoices'][0]['invoice_no']:'oodler-expreess-'.time()) }}</h1> --}}
        <h1>INVOICE:{{ $invoice_no }} </h1>
        <div id="company" class="clearfix">

            <div><span>Date:</span> {{ date('d/m/Y', time()) }}</div>
            

            <div><span>Company: </span>Oodler Express</div>
            <div><span>NY & NJ, 718-218-5239</div>
            <div><span>Email: </span> <a href="mailto:sales@oodlerexpress.com">sales@oodlerexpress.com</a></div>
            <div><span>Total Cost: $</span> @php
               echo $totalCost= $delivery['quote_agreed_cost']['quoted_price']+$delivery['quote_agreed_cost']['extra_charges'];
            @endphp</div>
        

        </div>
        <div><span>Business Name:</span>{{$delivery['customer']['business_name']}}</div>
        <div><span>Business Email: </span> <a href="mailto:{{$delivery['customer']['business_email']}}">{{$delivery['customer']['email']}}</a></div>
        <div><span>PO Number: </span>{{$delivery['po_number']}}</div>
        <div><span><strong>Pick-up :</strong> </span>{{$delivery['pickup_street_address']}}</div>
        <div>{{$delivery['pickup_city']}},{{$delivery['pickup_state']}}, {{$delivery['pickup_zipcode']}}</div>
        <div><span>Pick-up Date : </span>{{$delivery['pickup_date']}}</div>
        <div><span>Unit : </span>{{$delivery['pickup_unit']}}</div>
        <div><span>Contact No. : </span>{{$delivery['pickup_contact_number']}}</div>
        <div><span><strong>Drop-off :</strong> </span>{{$delivery['drop_off_street_address']}}</div>
        <div>{{$delivery['drop_off_city']}},{{$delivery['drop_off_state']}}, {{$delivery['drop_off_zipcode']}}</div>
        <div><span>Drop-off Date : </span>{{$delivery['drop_off_date']}}</div>
        <div><span>Unit : </span>{{$delivery['drop_off_unit']}}</div>
        <div><span>Contact No. : </span>{{$delivery['drop_off_contact_number']}}</div>
        
        {{--  Event Detail is starting --}}
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th class="service">DATE</th>
                    <th class="desc">PAYEE NAME</th>
                    <th class="total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                
                    {{-- <tr>

                        <td colspan="5" class="text-center alert-secondary"><strong> Payment Received:</strong></td>
                    </tr> --}}
               
                <?php 
            $recievedAmount=0;
            $k=5;
            if(isset($total_amount_to_pay) && $total_amount_to_pay>0)
            $totalCost=$total_amount_to_pay;

            foreach ($delivery['invoices'] as $key=>$invoice){ 
               
                $recievedAmount=$recievedAmount+$invoice['paid_amount'];
                $totalCost
                ?>

                <tr>
                    <td class="service">{{ date('d/m/Y', strtotime($invoice['created_at'])) }}</td>
                    <td class="desc">{{ $invoice['payee_name'] }}</td>
                    <td class="total">{{ $invoice['paid_amount'] }}</td>
                </tr>

                        <?php }?>

                <tr>
                    
                    <td class="grand total">&nbsp;</td>
                    <td class="grand total desc">Total Received:</td>
                    <td class="grand total">${{ !isset($recievedAmount) ? ($recievedAmount = 0) : $recievedAmount }}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="desc">Due Amount:</td>
                    <td>{{$totalCost-$recievedAmount}}</td>
                </tr>
               
            </tbody>
        </table>
        {{-- <div id="notices">
            <div>NOTE:</div>
            <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
        </div> --}}
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal. &copy;<div>
            {{ config('constants.app_name') }}</div>
    </footer>
</body>

</html>
