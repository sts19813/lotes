<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title>@yield('title', 'Dashboard')</title>
	<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

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