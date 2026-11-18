
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة رموز HMC</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            color: #222;
        }

        .toolbar {
            padding: 15px;
            text-align: center;
        }

        .toolbar button {
            padding: 10px 25px;
            cursor: pointer;
            margin: 5px;
        }

        .sheet {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2mm;
        }

        .label {
            height: 26.5mm;
            border: 0.3mm solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .label img {
            width: 20mm;
            height: 20mm;
            object-fit: contain;
        }

        .serial {
            font-size: 9pt;
            font-weight: bold;
            direction: ltr;
            margin-top: 1mm;
        }

        @media print {
            .toolbar {
                display: none;
            }

            .label {
                border: 0.2mm solid #ddd;
            }
        }
    </style>
</head>

<body>

<div class="toolbar">
    <h3>دفعة {{ $batch->batch_number }}</h3>

    <p>
        {{ $batch->first_serial }}
        -
        {{ $batch->last_serial }}
    </p>

    <button onclick="window.print()">طباعة الرموز</button>

    <button onclick="window.location.href='{{ route('qr.index') }}'">
        إغلاق
    </button>
</div>

<div class="sheet">

    @foreach($batch->qrCodes as $code)

        <div class="label">

            <img
                src="{{ asset('storage/'.$code->image_path) }}"
                alt="{{ $code->code }}"
            >

            <div class="serial">
                {{ $code->code }}
            </div>

        </div>

    @endforeach

</div>

</body>
</html>
