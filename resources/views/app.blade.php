<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>Currency Converter</title>

	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body>
	<div id="app">
		<main-component></main-component>
	</div>

	<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>