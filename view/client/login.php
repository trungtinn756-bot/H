<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng Nhập Độc Giả MANGATIN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            background-color: #0b0f19;
            color: #ffffff;
            font-family: "Inter", sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-[#111827] border border-gray-800 rounded-2xl shadow-2xl p-6 md:p-8 space-y-6">
        <div class="text-center space-y-2">
            <a href="index.php?action=home"
                class="text-3xl font-black bg-gradient-to-r from-indigo-500 to-cyan-400 bg-clip-text text-transparent tracking-wider">MANGA
                <span class="text-white">TIN</span></a>
            <p class="text-xs text-gray-400">Chào mừng trở lại! Vui lòng đăng nhập tài khoản của bạn</p>
        </div>
        <form action="index.php?action=login" method="post" class="space-y-4">
            <?php if (!empty($login_success_message)): ?>
                <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    <?php echo htmlspecialchars($login_success_message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>
            <div class="space-y-1.5">
                <label for="identity" class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tên tài khoản
                    hoặc Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500 text-sm">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" id="identity" name="identity"
                        class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        placeholder="Nhập tên tài khoản hoặc email" required />
                </div>
            </div>
            <div class="space-y-1.5">
                <label for="password" class="text-xs text-gray-400 font-bold uppercase tracking-wider">Mật khẩu</label>
                <div class="relative">
                    <span class="absolute left-4 top-3 text-gray-500 text-sm">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password"
                        class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        placeholder="••••••••" required />
                    <button type="button" id="togglePassword" class="absolute right-4 top-3 text-gray-500">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between pt-1">
                <label for="remember" class="flex items-center gap-2 text-xs text-gray-400 cursor-pointer select-none">
                    <input type="checkbox" id="remember" name="remember" class="accent-indigo-500 rounded">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="index.php?action=forgot-password" class="text-xs text-indigo-400 hover:underline">
                    Quên mật khẩu?
                </a>
            </div>
            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-600 to-cyan-500 text-white font-bold py-3 rounded-xl text-sm shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                Đăng Nhập Hệ Thống
            </button>
        </form>
        <div class="text-center border-t border-gray-800/80 pt-4">
            <p class="text-xs text-gray-400">Bạn là độc giả mới?
                <a href="index.php?action=register" class="text-indigo-400 font-semibold hover:underline">Tạo tài khoản
                    mới ngay</a>
            </p>
        </div>
    </div>

    <script>
        // Lấy các phần tử dựa vào ID đã đặt ở trên
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        // Lắng nghe sự kiện click vào nút con mắt
        togglePasswordButton.addEventListener('click', function () {
            // Kiểm tra loại thuộc tính hiện tại của ô input
            if (passwordInput.type === 'password') {
                // Nếu đang ẩn (password) -> chuyển sang hiện (text)
                passwordInput.type = 'text';

                // Đổi icon thành mắt gạch chéo (nếu dùng Font Awesome v6)
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                // Nếu đang hiện (text) -> chuyển về ẩn (password)
                passwordInput.type = 'password';

                // Đổi icon quay lại mắt bình thường
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</body>

</html>