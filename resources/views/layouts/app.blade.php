<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Manajemen Surat</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    @include('sidebar')

    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            margin: 0;
            background: #f6f7fb;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        .app-shell {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: stretch !important;
            min-height: 100vh;
            width: 100%;
        }

        .content {
            flex: 1 1 0% !important;
            min-width: 0 !important;
            width: auto !important;
            overflow-x: hidden;
        }

        @media (max-width: 767px) {
            .app-shell {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
