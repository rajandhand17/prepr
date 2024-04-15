<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vitereactrefresh
    @vite(['resources/js/scorm-player/app.jsx'])
</head>
<body>
<div id="preplab-scorm-player"></div>
<script>
    window.scorm_uuid = "{{$scorm_uuid ?? null}}"
</script>
</body>
</html>
