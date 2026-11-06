<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GEZUX is the global clipping performance marketplace connecting brands with creators to turn long-form content into viral distribution.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GEZUX — The Global Clipping Marketplace</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/images/gezux-favicon.jpg">

    <!-- Fonts: Inter & Manrope -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Vite Scripts & CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
</head>
<body class="bg-[#070708] text-[#F7F7F5] antialiased overflow-x-hidden min-h-screen">
    <div id="app"></div>
</body>
</html>
