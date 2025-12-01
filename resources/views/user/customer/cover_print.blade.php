<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envelope Design</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;

            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .envelope {
            width: 700px;
            height: 370px;
            background-color: #ffffff;
            /* border: 2px solid #ccc; */
            padding: 20px;
            box-sizing: border-box;
            position: relative;
        }

        .tagline {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }

        .to {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .address {
            margin-top: 5px;
            font-size: 14px;
            line-height: 1.5;
        }

        .phone {
            margin-top: 10px;
            font-size: 14px;
        }

        @media print {
            .envelope {
                transform: rotate(270deg);
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    @foreach ($customer as $data)
    <div class="envelope">
        <div style="text-align: center; margin-top: 2px;margin-left: 270px;">{{ @$data->courier->name }}</div>
        <div style="margin-left: 100px;">
            <div class="to">
                TO,
            </div>
            <div class="address" style="width: 220px !important;">
                <strong>{{ $data->name }}</strong><br>
                {{ $data->address }}<br>
                {{ $data->area }}<br>
                <strong>{{ $data->city?->name }}, {{ $data->state?->name }}-{{ $data->pincode }}</strong><br>
            </div>
            <div class="phone">
                Ph: {{ $data->mobile }} ({{ $data->contact_person }})
            </div>
        </div>
    </div>
    @endforeach
</body>
<script>
    window.print();
</script>

</html>
