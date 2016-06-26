<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield("title")</title>

    <!-- css -->
    <link rel="stylesheet" href="{{ asset("css/bootstrap.css") }}">
    <link rel="stylesheet" href="{{ asset("css/master.css") }}">

    <!-- javascript -->
    <script src="{{ asset("js/jquery-1.12.4.js") }}"></script>
    <script src="{{ asset("js/bootstrap.js") }}"></script>

</head>
<body>
    
    @include("layouts.header")

    @include("layouts.navigation")
    
    <div class="container" id="belowtopnav">
        @yield("content")
    </div>

    @include("layouts.footer")
    
    <!-- javascript -->
    <script src="{{ asset("js/master.js") }}"></script>
</body>
</html>