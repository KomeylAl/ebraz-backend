<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتور</title>
    <style>
        @font-face {
            font-family: 'B Nazanin';
            src: url('https://ebraz.viona-graphy.ir/{{ ('fonts/BNazanin.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'B Nazanin', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 30px;
            font-weight: bold;
        }
        .invoice-header p {
            margin: 5px 0;
            font-size: 18px;
        }
        .invoice-details {
            margin: 30px;
        }
        .invoice-details .client-info, .invoice-details .invoice-info {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .invoice-details .client-info {
            text-align: right;
        }
        .invoice-details .invoice-info {
            text-align: right;
        }
        .invoice-details h3 {
            margin-bottom: 5px;
            font-size: 18px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            padding: 10px;
            text-align: right;
            border: 1px solid #ccc;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .table td {
            font-size: 16px;
        }
        .total {
            text-align: left;
            font-size: 20px;
            font-weight: bold;
        }
        .sign {
            margin: 20px;
            font-size: 18px;
        }
        @page {
            size: A4;
            margin: 20mm;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="invoice-header">
        <h1>فاکتور</h1>
        <p>کلینیک ابراز</p>
        <p>آدرس: اصفهان، خ هزارجریب، خ آزادی یا کلینی (مرداویج)<br /> خ ملاصدرای جنوبی، بن بست شاهد، پلاک ۹</p>
    </div>

    <div class="invoice-details">
        <div class="client-info">
            <h3>اطلاعات مراجع</h3>
            <p>نام: {{ $client['name'] }}</p>
            <p>تلفن: {{ $client['phone'] }}</p>
        </div>

        <div class="invoice-info">
            <h3>اطلاعات فاکتور</h3>
            <p>تاریخ: {{ $date }}</p>
        </div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>پزشک</th>
            <th>تاریخ مراجعه</th>
            <th>مبلغ</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                <td>{{ $item['doctor'] }}</td>
                <td>{{ $item['date'] }}</td>
                <td>{{ number_format($item['amount']) }} <span>تومان</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>مجموع:  {{ $total }} <span>تومان</span></p>
    </div>

    <div class="sign">
        مهر و امضا
    </div>
</div>

</body>
</html>
