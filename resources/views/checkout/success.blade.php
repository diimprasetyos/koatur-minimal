<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            padding: 48px 40px;
            text-align: center;
            max-width: 440px;
            width: 100%;
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 32px;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: background .2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn-ghost {
            display: inline-block;
            margin-top: 12px;
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
        }

        .btn-ghost:hover {
            color: #2563eb;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="icon-wrap">✓</div>
        <h1>Pembayaran Berhasil!</h1>
        <p>Terima kasih! Pembayaran Anda telah kami terima. Langganan Anda akan segera aktif. Cek email untuk konfirmasi.</p>
        <a href="{{ url('/register') }}" class="btn">Buat Akun Sekarang</a>
        <br>
        <a href="{{ url('/') }}" class="btn-ghost">← Kembali ke Beranda</a>
    </div>
</body>

</html>