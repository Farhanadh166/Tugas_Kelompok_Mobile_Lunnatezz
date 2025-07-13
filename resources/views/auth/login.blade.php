<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Lunneettez</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0c3fc 100%);
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(160, 120, 200, 0.15);
            padding: 40px 32px;
            text-align: center;
            max-width: 400px;
        }
        h2 {
            color: #7c3aed;
            margin-bottom: 24px;
            font-size: 2rem;
        }
        input[type="email"], input[type="password"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #a78bfa;
            border-radius: 8px;
            font-size: 1rem;
        }
        .btn {
            display: inline-block;
            margin: 16px 0 8px 0;
            padding: 12px 32px;
            background: #7c3aed;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn:hover {
            background: #5b21b6;
        }
        .link {
            color: #7c3aed;
            text-decoration: none;
            font-size: 1rem;
        }
        .link:hover {
            text-decoration: underline;
        }
        .error {
            color: #e53e3e;
            margin-top: 10px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Admin Lunneettez</h2>
        <form method="POST" action="{{ url('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" class="btn">Login</button>
        </form>
        <a href="{{ url('register') }}" class="link">Daftar Admin</a>
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        @if(session('success'))
            <div class="success" style="color:#16a34a; margin-bottom:10px; font-weight:bold;">{{ session('success') }}</div>
        @endif
    </div>
</body>
</html> 