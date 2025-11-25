<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', default: 'Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tabs button.active {
            background: #000;
            color: #fff;
        }
        .floor-plan {
            border: 1px solid #ddd;
            background: #fff;
            height: 650px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .metrics-bar {
            border-top: 1px solid #ccc;
            padding: 30px 0;
            background: #fafafa;
        }
        .metric-number {
            font-size: 28px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    

	@yield('content')

	@stack('scripts')
</body>
</html>