<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* .container {
	width: 80%;
	margin: auto;
	padding: 20px;
	border: 1px solid #000;
} */

    header {
        text-align: center;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
    }

    header p {
        margin: 5px 0;
        font-size: 14px;
    }

    hr {
        border: 1px solid #000;
    }

    .quotation-details {
        display: flex !important;
        justify-content: space-between;
    }

    .quotation-details .ref-info,
    .quotation-details .client-info {
        font-size: 14px;
    }

    .subject {
        font-size: 14px;
    }

    .item-table {
        border-collapse: collapse;
    }

    .item-table th,
    .item-table td {
        border: 1px solid #000;
        text-align: center;
        font-size: 14px;
    }

    .summary {
        display: flex;
        justify-content: space-between;
    }

    .summary .left-info {
        font-size: 14px;
    }

    .summary .right-info table {
        border-collapse: collapse;
        font-size: 14px;
    }

    .summary .right-info table td {
        text-align: right;
    }

    .bank-details {
        font-size: 14px;
    }

    .notes {
        font-size: 14px;
    }

    footer {
        font-size: 14px;
    }
</style>

<body>
    <!-- class="container" -->
    <div class="content">
        <header>
            <h1>PROCESSO PLAST ENT. PVT. LTD.</h1>
            <p>236, Latiwala Estate, Nr. Bhakti Nagar,
                <br>Bapunagar , Ahmedabad - 380024, Gujarat
            </p>
            <p>Tel: 079-2295978, Office: 99095 05978</p>
            <p>GST No: 24AABCP6888M1ZR</p>
            <p>Email: info@spectacase.com, Website: www.spectacase.com, www.myoptician.in</p>
            <hr>
            <h3 style="padding: 0px;margin: 0px">Quotation</h3>
            <hr>
        </header>

        <div class="quotation-details">
            <div class="ref-info">
                <div><strong>Ref No :</strong> {{ $orders[0]->order_code ?? '' }}</div>
                <div style="text-align: right;"><strong>Ref Date :</strong> {{ date('d-m-Y', strtotime($orders[0]->date)) ?? '' }}</>
                </div>
            </div>
            <div class="quotation-details">
                <div class="client-info" style="padding-left: 20px;">
                    <p style="margin-bottom: 0px;font-size: 13px"><strong>M/s. {{ $orders[0]?->customer->name }} {{$orders[0]->company_name ? '('.$orders[0]->company_name.')' : ''}}</strong></p>
                    <div style="padding-left: 30px;">
                        <p style="margin-top: 0px;margin-bottom: 0px;">{{$orders[0]->customer?->address}} {{ $orders[0]->customer?->address2 }}</p>
                        <p style="margin-top: 0px;margin-bottom: 0px;">{{$orders[0]->customer?->city?->name}}, {{$orders[0]?->customer?->state?->name}}, {{$orders[0]->customer->pincode}}</p>
                        <p style="margin-top: 0px;margin-bottom: 0px;">M no. {{$orders[0]->customer?->mobile}} GST No. {{$orders[0]->customer?->gst}}</p>
                    </div>
                </div>
            </div>

            <div class="subject" style="padding-left: 50px;">
                <p style="font-size: 13px;"><strong>Subject : Quotation for your required Items</strong></p>
                <p style="font-size: 13px;margin-bottom: 0px"><strong>Dear Sir,</strong></p>
                <p style="font-size: 13px;margin-top: 0px">We thank you for your inquiry and are pleased to provide our most competitive and attractive offer for your referenced requirement as follows.</p>
            </div>

            <table class="item-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Model Name</th>
                        <th>Printing Details</th>
                        <th>Item Remark</th>
                        <th>Qty (Pcs)</th>
                        <th>Rate (Pcs)</th>
                        <th>Extra Dis</th>
                        <th>Total (Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1 @endphp
                    @foreach ($orders[0]?->orderDetails as $item)
                    <tr>
                        <td style="border-right: none;border-bottom:none;border-top: none;">{{$i}}</td>
                        <td style="border-right: none;border-bottom:none;border-top: none; text-align: left">{{$item->item_name}} {{ $orders[0]?->printTypeExtra?->code ? '- '.$orders[0]?->printTypeExtra?->code : '' }}{{ $item?->print_type_name ? '- '.$item?->print_type_name : '' }}</td>
                        <td style="border-right: none;border-bottom:none;border-top: none; text-align: left">{{$item->narration}}</td>
                        <td style="border-right: none;border-bottom:none;border-top: none; text-align: left">{{$item->remark}}</td>
                        <td style="border-right: none;border-bottom:none;border-top: none;">{{ round($item->total_qty) }}</td>
                        <td style="border-right: none;border-bottom:none;border-top: none;">{{ round($item->total_rate) }}</td>
                        <td style="border-right: none;border-bottom:none;border-top: none;">{{ round($item->discount) }}%</td>
                        <td style="border-bottom:none;border-top: none;">{{ round($item->total_amount) }}</td>
                    </tr>
                    @php $i++ @endphp
                    @endforeach
                    @if ($i < 6)
                    @for ($j = $i; $j <= 110 - $i; $j++)
                    <tr>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-right: none;border-bottom:none;border-top: none;"></td>
                        <td style="border-bottom:none;border-top: none;"></td>
                    </tr>
                    @endfor
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">
                            <div style="text-align: left;">
                                <p style="margin-top: 0px;margin-bottom: 0px;"><strong style="padding-right: 40px;">Delivery</strong><strong style="padding-right: 20px;">:</strong> 10 Days after received payment</p>
                                <p style="margin-top: 0px;margin-bottom: 0px;"><strong style="padding-right: 29px;">Transport</strong><strong style="padding-right: 20px;">:</strong> {{ $orders[0]->customer?->transport?->name }}</p>
                                <p style="margin-top: 2px;margin-bottom: 0px;"><strong style="padding-right: 45px;">Validity</strong><strong style="padding-right: 20px;">:</strong> 15 Days</p>
                                <p style="margin-top: 2px;margin-bottom: 0px;"><strong style="padding-right: 32px;">Payment </strong><strong style="padding-right: 20px;">:</strong> 100% Advance</p>
                            </div>
                        </td>
                        <!-- <td rowspan="2"></td> -->
                        <td rowspan="1" colspan="4">
                            <div style="text-align: right;">
                                <p style="font-size: 13px;margin-top: 0px;margin-bottom: 0px;"><strong>Basic Amount </strong>:</p>
                                <p style="font-size: 13px;margin-top: 2px;margin-bottom: 0px;"><strong>Disc. &nbsp;&nbsp; {{ round($orders[0]->discount) }} &nbsp;% </strong>:</p>
                                @if (isset($orders[0]->redeem_coin) && $orders[0]->redeem_coin > 0)
                                <p style="font-size: 13px;margin-top: 2px;margin-bottom: 0px;"><strong>Redeem Coin &nbsp;&nbsp;&nbsp;</strong></p>
                                @endif
                                <p style="font-size: 13px;margin-top: 2px;margin-bottom: 0px;"><strong>Net Amount </strong>:</p>
                            </div>
                        </td>
                        <td rowspan="1">
                            <div style="text-align: center;">
                                <p style="font-size: 13px;margin-top: 0px;margin-bottom: 0px;">{{ round($orders[0]->total_amount) }}</p>
                                <p style="font-size: 13px;margin-top: 2px;margin-bottom: 0px;">{{ round($orders[0]->discount_amount) }}</p>
                                @if (isset($orders[0]->redeem_coin) && $orders[0]->redeem_coin > 0)
                                <p style="font-size: 13px;margin-top: 2px;margin-bottom: 0px;">{{ round($orders[0]->redeem_coin) }}</p>
                                @endif
                                <p style="font-size: 13px;margin-top: 2px;margin-bottom: 0px;"><strong>{{ round($orders[0]->net_amount) }} </strong></p>
                            </div>
                        </td>
                    </tr>
                    <tr style="padding: 0px;">
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div style="text-align: left;">
                                <p style="font-size: 13px;margin-top: 0px;margin-bottom: 0px;"><strong style="padding-right: 62px;">Bank</strong><strong style="padding-right: 20px;">:</strong> HDFC BANK (Ashram Road)</p>
                                <p style="font-size: 13px;margin-top: 0px;margin-bottom: 0px;"><strong style="padding-right: 52px;">A/c No</strong><strong style="padding-right: 20px;">:</strong> 00692560008424</p>
                                <p style="font-size: 13px;margin-top: 0px;margin-bottom: 0px;"><strong style="padding-right: 60px;">IFSC </strong><strong style="padding-right: 20px;">:</strong> HDFC0000069</p>
                            </div>
                        </td>
                        <td colspan="5" style="text-align: left; font-size: 10px;padding: 0px">
                            Notes :
                            <p style="margin-top: 0px;margin-bottom: 0px;"> >> All prices include 18% GST. </p>
                            <p style="margin-top: 0px;margin-bottom: 0px;"> >> Order conformation consider after share payment receipt or transction id </p>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div style="padding-bottom: 0px !important;margin-bottom: 0px !important">
                <p style="padding-left: 50px; font-size: 13px;margin-bottom: 0px;">We trust that our offer meets your expectations and look forward to the opportunity of receiving your valued order. Rest assured, it will be handled with the utmost care and attention. We sincerely thank you for your continued trust and confidence. </p>
                <!-- <p style="font-size: 13px">Once again thanking your kindness and commanding your confidence at all times.</p> -->
                <p style="font-size: 13px">Your's faithfully</p>
                <p style="font-size: 13px;padding: 0px"><strong>For, Processo Plast Ent. Pvt Ltd.</strong></p>
                <p style="padding: 0px;">Authorized Signatory</p>
            </div>
        </div>
    </div>
</body>
<!-- <script>
    window.print();
</script> -->

</html>
