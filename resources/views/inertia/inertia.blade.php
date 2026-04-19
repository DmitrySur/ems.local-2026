<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Title по умолчанию -->
    <title inertia>АСУ-ЭМС</title>
    <!-- Vite -->
    @routes('app')

    @vite('resources/js/inertia/app.js')
    <!-- Inertia meta -->
    @inertiaHead
</head>
<body class="layout-fluid">
@inertia
</body>
</html>
