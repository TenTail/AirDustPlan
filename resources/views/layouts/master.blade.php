<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield("title")</title>

    <!-- css -->
    <link rel="stylesheet" href="{{ asset("css/bootstrap.css") }}">

    <!-- javascript -->
    <script src="{{ asset("js/jquery-1.12.4.js") }}"></script>
    <script src="{{ asset("js/bootstrap.js") }}"></script>

</head>
<body>

    @yield("content")

</body>
</html>