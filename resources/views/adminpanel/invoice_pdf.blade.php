<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>INVOICE {{ $invoice_no }}</title>

    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: verdana;
            font-size: 14px;
            font-family: verdana;
            max-width: 700px;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
            width: 50%;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
            width: 48%;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }
        #invoice h2 {
            color: #D08E3E;
            font-size: 16px;
            line-height: 1em;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 20px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
            text-align: left;
        }

        table .qty {text-align: left;}

        table .total {
            background: #57B223;
            color: #FFFFFF;
            text-align: right
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
        #logo span{
                color: #54595F;
                font-size: 25px;
                font-weight: 700;
                margin-left: -28px;
            }
    </style>
</head>
@php
        
        $overtime =12;
      //$logo_url='https://office.oodlerexpress.com/adminpanel/dist/img/2.png';
      $logo_url='http://127.0.0.1:8000/adminpanel/dist/img/2.png';
      //$logo_url=storage_path('app/public/2.png');
    @endphp
<body>
    <header class="clearfix">
        <div id="logo" style="color: #CF8E2F; font-size:60px; font-weight: 700;">
          oodler <span>Express</span>
            {{-- <img alt="OODLER EXPRESS" src="{{$logo_url}}"
                width="100px" height="100px"> --}}
        </div>
        <div id="company">
            <h2 class="name">OODLER EXPRESS</h2>
            <div>Servicing NY & NJ, US</div>
            <div>718-218-5239</div>
            <div><a href="mailto:sales@oodlerexpress.com">sales@oodlerexpress.com</a></div>
        </div>
        </div>
    </header>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">INVOICE TO:</div>
                <h2 class="name">{{$delivery['customer']['business_name']}}</h2>
                <div class="address">{{$delivery['customer']['business_address']}}</div>
                <div class="email"><a href="mailto:{{$delivery['customer']['business_email']}}">{{$delivery['customer']['email']}}</a></div>
                <div><span><strong>Pick-up :</strong> </span>{{$delivery['pickup_street_address']}}</div>
                {!!($delivery['pickup_zipcode']!='')?'<div>'.$delivery['pickup_zipcode'].'</div>':''!!}
                <div><span>Pick-up Date : </span>{{$delivery['pickup_date']}}</div>
                {!!($delivery['pickup_unit']!='')?'<div>'.$delivery['pickup_unit'].'</div>':''!!}
                <div><span><strong>Drop-off :</strong> </span>{{$delivery['drop_off_street_address']}}</div>
                {!!($delivery['drop_off_zipcode']!='')?'<div>'.$delivery['drop_off_zipcode'].'</div>':''!!}
                <div><span>Drop-off Date : </span>{{$delivery['drop_off_date']}}</div>
                {!!($delivery['drop_off_unit']!='')?'<div>'.$delivery['drop_off_unit'].'</div>':''!!}
                
                
              </div>
            <div id="invoice">
                <h1>INVOICE {{ $invoice_no }}</h1>
                <h3>PO NO#: {{$delivery['po_number']}}</h3>
                <div class="date">Date of Invoice: {{ date(config('constants.date_formate'), time()) }}</div>
                <h3><span>Total Cost: $</span> @php
                  echo $totalCost= $delivery['quote_agreed_cost']['quoted_price']+$delivery['quote_agreed_cost']['extra_charges'];
               @endphp</h3>
            </div>
        </div>
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2"><strong>DELIVERY COST</strong></td>
                    <td><strong>${{$totalCost}}</strong></td>
                </tr>
                <tr>
                    <th class="no">#</th>
                    {{-- <th class="desc">DESCRIPTION</th> --}}
                    <th colspan="2" class="unit">DATE</th>
                    <th class="qty">PAYEE NAME</th>
                    <th class="total">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
               
              <?php 
            $recievedAmount=0;
            $k=1;
            if(isset($total_amount_to_pay) && $total_amount_to_pay>0)
            $totalCost=$total_amount_to_pay;
//p($delivery);
            foreach ($delivery['invoices'] as $key=>$invoice){ 
               
                $recievedAmount=$recievedAmount+$invoice['paid_amount'];
                $totalCost
                ?>
                
                <tr>
                    <td class="no">{{$k++}}</td>
                    {{-- <td class="desc">
                        <h3>Payment Received</h3>
                    </td> --}}
                    <td colspan="2" class="unit">{{ date(config('constants.date_formate'), strtotime($invoice['created_at'])) }}</td>
                    <td  class="qty">{{ $invoice['payee_name'] }}</td>
                    <td class="total">${{ $invoice['paid_amount'] }}</td>
                </tr>
                <?php }?>
                
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">TOTAL PAID AMOUNT</td>
                    <td>${{ !isset($recievedAmount) ? ($recievedAmount = 0) : $recievedAmount }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="2"></td>
                    <td colspan="2">DELIVERY COST</td>
                    <td>${{$totalCost}}</td>
                </tr> --}}
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="color:red">DUE AMOUNT</td>
                    <td style="color:red">${{ $due_amount =$totalCost- $recievedAmount }}</td>
                </tr>
            </tfoot>
        </table>
        {{-- <div id="thanks">Thank you!</div> --}}
        {{-- <div id="notices">
            <div>NOTICE:</div>
            <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
        </div> --}}
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal.
    </footer>
</body>

</html>
