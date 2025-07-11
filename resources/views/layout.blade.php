<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        body {
            background: #f7f8fa;
            font-family: sans-serif;
            margin: 0; padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            display: block;
            width: 100%;
            padding: 14px;
            background: #34a853;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover { background: #2c8e42; }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    @stack('scripts')
</div>
</body>
