<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Admin Kue Mang Wiro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Card Grid Layout -->
            <div class="grid grid-cols-1">
                <!-- Logo Section -->
                <div class="bg-gradient-to-br from-[#ffffff] to-[#ffffff] p-8 flex items-center justify-center">
                    <div class="w-full max-w-[180px]">
                        <img src="{{ asset('images/logoke1.png') }}" 
                             alt="Kue Balok Mang Wiro" 
                             class="w-full h-auto object-contain">
                    </div>
                </div>
                
                <!-- Form Section -->
                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Sign In</h1>
                        <p class="text-sm text-gray-600">Masukkan email dan password Anda</p>
                    </div>
                    
                    <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                                <ul class="list-none space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li class="text-sm flex items-start">
                                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $error }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Email Input -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   placeholder="nama@email.com" 
                                   value="{{ old('email') }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent outline-none transition-all duration-200 text-gray-900 placeholder-gray-400">
                        </div>
                        
                        <!-- Password Input -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   placeholder="Masukkan password"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2e4358] focus:border-transparent outline-none transition-all duration-200 text-gray-900 placeholder-gray-400">
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="remember" 
                                   name="remember"
                                   {{ old('remember') ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#2e4358] border-gray-300 rounded focus:ring-[#2e4358] focus:ring-2 cursor-pointer">
                            <label for="remember" class="ml-2 text-sm text-gray-700 cursor-pointer">
                                Ingat saya
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-[#2e4358] text-white py-3 px-4 rounded-lg font-semibold hover:bg-[#1a2a3a] focus:outline-none focus:ring-2 focus:ring-[#2e4358] focus:ring-offset-2 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Footer Text -->
        <p class="text-center text-sm text-gray-600 mt-6">
            © 2025 Kue Mang Wiro. All rights reserved.
        </p>
    </div>
</body>
</html>

