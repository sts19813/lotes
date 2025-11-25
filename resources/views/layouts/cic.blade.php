<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title>@yield('title', 'Dashboard')</title>
	<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
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

	<script>
		var hostUrl = "{{ asset('assets') }}/";
	</script>
	<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
	<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
	<!--end::Global Javascript Bundle-->

	@stack('scripts')
</body>
</html>