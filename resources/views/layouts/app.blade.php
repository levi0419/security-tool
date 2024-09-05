<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Web Security Audit Tool')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header>
        <nav class="navbar">
            <a class="navbar-brand">Web Security Audit Tool</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/scan') }}">Scan</a>
                </li>
            </ul>
        </nav>
    </header>

    <div class="container">
        @yield('content')
    </div>

    @include('partials.footer')
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
