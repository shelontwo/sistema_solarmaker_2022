<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="robots" content="noindex" />
  <link rel="icon" href="{{ url('img/logo.png') }}">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Styles -->
  <link href="{{ asset('css/all.css') }}" rel="stylesheet">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/plugins/monthSelect/style.css"> 
  <script src="https://npmcdn.com/flatpickr/dist/flatpickr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/plugins/rangePlugin.d.ts"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/plugins/rangePlugin.js"></script>  -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script> 
  <script src="{{ asset('node_modules/tinymce/tinymce.js') }}"></script>
  <!-- <script src="https://cdn.tiny.cloud/1/2zjdtoyzvo7gwu4udnomztf584herrkb42a9wdkvr84xeq1d/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script> -->
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

@yield('body')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/cms/script.min.js') }}"></script>
<script>
$(document).ready(function() {
  $('[data-toggle="tooltip"]').tooltip();
});
</script>

</html>