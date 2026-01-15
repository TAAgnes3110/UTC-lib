@extends('layouts.app')

@section('title', 'Đăng ký - Hệ thống Quản lý Thư viện UTC')

@section('content')
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" class="min-h-screen flex items-center justify-center p-4 py-8">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8">
        <!-- Logo và Tiêu đề -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-full mb-4">
                <i class="fas fa-user-plus text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Đăng ký tài khoản</h1>
            <p class="text-gray-600">Tạo tài khoản mới để sử dụng hệ thống</p>
        </div>

        <!-- Form đăng ký -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Họ -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-indigo-600"></i>Họ
                    </label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        placeholder="Nguyễn"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('first_name') border-red-500 @enderror"
                        required
                    >
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tên -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-indigo-600"></i>Tên
                    </label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        placeholder="Văn A"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('last_name') border-red-500 @enderror"
                        required
                    >
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
                    placeholder="student@utc.edu.vn"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                    required
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Mật khẩu -->
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
                        onclick="togglePassword('password')"
                    >
                        <i class="fas fa-eye" id="eyeIcon1"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-indigo-600"></i>Xác nhận mật khẩu
                </label>
                <div class="relative">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        required
                    >
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        onclick="togglePassword('password_confirmation')"
                    >
                        <i class="fas fa-eye" id="eyeIcon2"></i>
                    </button>
                </div>
            </div>

            <!-- Số điện thoại -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone mr-2 text-indigo-600"></i>Số điện thoại
                </label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="0912345678"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                >
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-start">
                <input
                    type="checkbox"
                    id="terms"
                    class="mt-1 w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    required
                >
                <label for="terms" class="ml-2 text-sm text-gray-600">
                    Tôi đồng ý với
                    <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">Điều khoản sử dụng</a>
                    và
                    <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">Chính sách bảo mật</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg hover:shadow-xl"
            >
                <i class="fas fa-user-plus mr-2"></i>Đăng ký
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Đã có tài khoản?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    Đăng nhập ngay
                </a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(inputId === 'password' ? 'eyeIcon1' : 'eyeIcon2');

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
