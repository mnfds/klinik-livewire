<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <style>
        /* ======================
            404 Page Template
        ======================= */

        body {
            margin: 0;
            font-family: 'Arvo', serif;
            background: #fff;
        }

        .page_404 {
            padding: 40px 0;
            text-align: center;
        }

        .four_zero_four_bg {
            
            background-image: url('https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif');
            height: 400px;
            background-position: center;
            background-repeat: no-repeat;
        }

        .four_zero_four_bg h1 {
            font-size: 80px;
            margin: 0;
        }

        .contant_box_404 {
            margin-top: -50px;
        }

        .contant_box_404 h3 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .contant_box_404 p {
            color: #666;
            margin-bottom: 20px;
        }

        .link_404 {
            color: #fff !important;
            padding: 10px 20px;
            background: #39ac31;
            display: inline-block;
            text-decoration: none;
            border-radius: 4px;
        }

        .link_404:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<section class="page_404">
    <div class="four_zero_four_bg">
        <h1>@yield('code')</h1>
    </div>

    <div class="contant_box_404">
        <h3>@yield('message')</h3>
        <p>@yield('description')</p>

        <a href="{{ url('/') }}" class="link_404">
            Kembali
        </a>
    </div>
</section>

</body>
</html>
