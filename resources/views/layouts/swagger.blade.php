<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('l5-swagger.documentations.'.$documentation.'.api.title')}}</title>

	<!-- Scripts -->
	<script src="{{ asset('js/app.js') }}" defer></script>

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">

    <style>
		html {
			box-sizing: border-box;
			overflow: -moz-scrollbars-vertical;
			overflow-y: scroll;
		}

		*, *:before, *:after {
			box-sizing: inherit;
		}

		body {
			margin:0;
			background: #fafafa;
		}
    </style>
</head>
<body>
	<div id="app">
		@include('layouts.partials.nav')
	</div>

	@yield('content')

	<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
	<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
	<script>
		window.onload = function() {
			// Build a system
			const ui = SwaggerUIBundle({
				dom_id: '#swagger-ui',
				url: "{!! $urlToDocs !!}",
				operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
				configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
				validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
				oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

				requestInterceptor: function(request) {
					request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
					return request;
				},

				presets: [
					SwaggerUIBundle.presets.apis,
					SwaggerUIStandalonePreset
				],

				plugins: [
					SwaggerUIBundle.plugins.DownloadUrl
				],

				layout: "StandaloneLayout",
				docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
				deepLinking: true,
				filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
				persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

			})

			window.ui = ui
		}
	</script>
</body>
</html>
