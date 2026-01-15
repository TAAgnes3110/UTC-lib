@extends('layouts.app')

@section('title', 'Đăng nhập - Hệ thống Quản lý Thư viện UTC')

@section('content')
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <!-- Logo và Tiêu đề -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-full mb-4">
                <i class="fas fa-book-open text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Thư viện UTC</h1>
            <p class="text-gray-600">Đăng nhập vào hệ thống</p>
        </div>

        <!-- Form đăng nhập -->
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-indigo-600"></i>Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="admin@utc.edu.vn"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                    required
                    autofocus
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-indigo-600"></i>Mật khẩu
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                        required
                    >
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        onclick="togglePassword()"
                    >
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember me & Forgot password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Quên mật khẩu?
                </a>
            </div>

            <!-- Error Message -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg hover:shadow-xl"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Chưa có tài khoản?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    Đăng ký ngay
                </a>
            </p>
        </div>

        <!-- Demo Accounts -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center mb-3">Tài khoản demo:</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-gray-50 p-2 rounded">
                    <strong class="text-gray-700">Admin:</strong>
                    <div class="text-gray-600">admin@utc.edu.vn</div>
                </div>
                <div class="bg-gray-50 p-2 rounded">
                    <strong class="text-gray-700">Student:</strong>
                    <div class="text-gray-600">student@utc.edu.vn</div>
                </div>
                <div class="bg-gray-50 p-2 rounded">
                    <strong class="text-gray-700">Librarian:</strong>
                    <div class="text-gray-600">librarian@utc.edu.vn</div>
                </div>
                <div class="bg-gray-50 p-2 rounded">
                    <strong class="text-gray-700">Lecturer:</strong>
                    <div class="text-gray-600">lecturer@utc.edu.vn</div>
                </div>
            </div>
            <p class="text-xs text-gray-400 text-center mt-2">Mật khẩu: 123456</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
