
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'HMC QR Generator')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #F8F7F4;
            color: #302D29;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #292724;
            position: fixed;
            right: 0;
            top: 0;
            padding: 25px 16px;
        }

        .sidebar h4 {
            color: #D4B477;
            text-align: center;
            font-weight: 800;
            margin-bottom: 35px;
        }

        .sidebar a {
            display: block;
            color: #EEEAE2;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 9px;
            margin-bottom: 7px;
            transition: .2s;
        }

        .sidebar a:hover {
            background: #49443B;
            color: #F0D49B;
        }

        .main {
            margin-right: 250px;
            padding: 30px;
        }

        .content-card {
            background: white;
            border: 1px solid #ECE8E0;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 3px 12px rgba(0,0,0,.025);
        }

        .gold {
            color: #B18A45;
        }

        .btn-gold {
            background: #B18A45;
            color: white;
            border: 0;
        }

        .btn-gold:hover {
            background: #967139;
            color: white;
        }

        @media(max-width: 800px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .main {
                margin-right: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<aside class="sidebar">
    <h4>HMC QR</h4>

    <a href="{{ route('dashboard') }}">⌂ لوحة التحكم</a>
    <a href="{{ route('qr.create') }}">＋ توليد رموز جديدة</a>
    <a href="{{ route('qr.index') }}">▦ سجل الرموز</a>
    <a href="{{ route('qr.batches') }}">▤ عمليات التوليد</a>
    <a href="{{ route('backup.index') }}">◈ النسخ الاحتياطي</a>
</aside>

<main class="main">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>
