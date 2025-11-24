<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Admin Kue Mang Wiro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('Animated-LoginForm-main/css/style.css') }}">
    <style>
        .error-message {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
            font-size: 12px;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            width: 100%;
        }
        .error-message ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .error-message li {
            margin: 5px 0;
        }
        .remember-me {
            display: flex;
            align-items: center;
            margin: 15px 0;
            width: 100%;
        }
        .remember-me input[type="checkbox"] {
            width: auto;
            margin: 0 8px 0 0;
            cursor: pointer;
        }
        .remember-me label {
            font-size: 13px;
            color: #333;
            cursor: pointer;
        }
        .container {
            width: 100%;
            max-width: 500px;
        }
        .form-container.sign-in {
            width: 100%;
            position: relative;
        }
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
            object-fit: contain;
        }
        .container button[type="submit"] {
            background-color: #2e4358;
            color: #fff;
        }
        .container button[type="submit"]:hover {
            background-color: #1a2a3a;
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="logo-container">
                    <img src="{{ asset('images/logoke1.png') }}" alt="Kue Balok Mang Wiro">
                </div>
                <h1>Sign In</h1>
                <span>or use your email and password</span>
                
                @if($errors->any())
                    <div class="error-message" role="alert">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <input type="email" 
                       name="email" 
                       id="email"
                       placeholder="Email" 
                       value="{{ old('email') }}"
                       required>
                <input type="password" 
                       name="password" 
                       id="password"
                       placeholder="Password"
                       required>
                
                <div class="remember-me">
                    <input type="checkbox" 
                           id="remember" 
                           name="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember me</label>
                </div>
                
                <button type="submit">Sign in</button>
            </form>
        </div>
    </div>
</body>
</html>

