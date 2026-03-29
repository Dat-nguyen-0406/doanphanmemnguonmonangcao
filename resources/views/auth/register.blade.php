<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AEON Mall - Đăng ký thành viên</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-magenta { color: #a61d6d; }
        .bg-aeon-magenta { background-color: #a61d6d; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4">

    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg border-t-8 border-aeon-magenta">
        <div class="text-center mb-8">
            <div class="inline-block bg-aeon-magenta text-white p-3 rounded-lg font-bold text-2xl mb-4 shadow-md">
                AEON
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Đăng ký thành viên</h2>
            <p class="text-sm text-gray-500 mt-1">Gia nhập cộng đồng AEON để nhận ngàn ưu đãi</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold text-sm mb-1">Đã có lỗi xảy ra:</p>
                <ul class="text-xs list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                        placeholder="Nguyễn Văn A">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                        placeholder="09xxx...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Địa chỉ Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                    placeholder="example@gmail.com">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Mật khẩu</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                        placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                        placeholder="••••••••">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Địa chỉ thường trú</label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                    placeholder="Số nhà, tên đường, phường/xã...">{{ old('address') }}</textarea>
            </div>

            <div class="flex items-center text-xs text-gray-500 py-2">
                <input type="checkbox" required class="mr-2 accent-[#a61d6d]">
                <span>Tôi đồng ý với các <a href="#" class="aeon-magenta underline">điều khoản dịch vụ</a> của AEON.</span>
            </div>

            <button type="submit" 
                class="w-full bg-aeon-magenta text-white font-bold py-3 rounded-md shadow-lg hover:bg-pink-800 transition duration-300 transform hover:-translate-y-0.5 uppercase tracking-wider">
                Đăng ký tài khoản
            </button>
        </form>

        <div class="mt-8 pt-6 border-t text-center">
            <p class="text-sm text-gray-600">
                Đã có tài khoản thành viên? 
                <a href="{{ route('login') }}" class="aeon-magenta font-bold hover:underline">Đăng nhập tại đây</a>
            </p>
        </div>
    </div>

</body>
</html>