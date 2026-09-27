<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Thành Viên - MANGATIN</title>
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
    <div class="w-full max-w-md bg-[#111827] border border-gray-800 rounded-2xl shadow-2xl p-6 md:p-8 space-y-5">
        <div class="text-center space-y-2">
            <a href="index.php?action=home"
                class="text-3xl font-black bg-gradient-to-r from-indigo-500 to-cyan-400 bg-clip-text text-transparent tracking-wider">MANGA
                <span class="text-white">TIN</span></a>
            <p class="text-xs text-gray-400">Đăng Ký Thành Viên MANGATIN</p>
        </div>
        <form action="index.php?action=register" method="post" class="space-y-3.5">
            <?php if (!empty($error)): ?>
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>
            <div class="space-y-1">
                <label for="username" class="text-[11px] text-gray-400 font-bold uppercase tracking-wide">Tên tài khoản
                    độc giả *</label>
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-gray-500 text-sm">
                        <i class="fa-solid fa-user-tag"></i>
                    </span>
                    <input type="text" id="username" name="username"
                        class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        placeholder="Nhập tên tài khoản" required />
                </div>
            </div>
            <div class="space-y-1">
                <label for="email" class="text-xs text-gray-400 font-bold uppercase tracking-wide">Địa chỉ Email liên
                    kết *</label>
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-gray-500 text-sm">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email"
                        class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        placeholder="Nhập email của bạn" required />
                </div>
            </div>
            <div class="space-y-1">
                <label for="password" class="text-xs text-gray-400 font-bold uppercase tracking-wider">Mật khẩu (Tối
                    thiểu 8 ký tự) *</label>
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-gray-500 text-sm">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password"
                        class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        placeholder="••••••••" required />
                    <button type="button" id="togglePassword"
                        class="absolute right-4 top-3 text-gray-500 hover:text-gray-300 text-sm">
                        <i id="eyeIcon" class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="space-y-1">
                <label for="confirm_password" class="text-xs text-gray-400 font-bold uppercase tracking-wider">Xác nhận
                    mật
                    khẩu*</label>
                <div class="relative">
                    <span class="absolute left-4 top-2.5 text-gray-500 text-sm">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="w-full bg-gray-950 border border-gray-800 rounded-xl pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
                        placeholder="••••••••" required />
                    <button type="button" id="toggleConfirmPassword"
                        class="absolute right-4 top-3 text-gray-500 hover:text-gray-300 text-sm">
                        <i id="confirmEyeIcon" class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-start pt-1">
                <label for=""
                    class="flex items-start gap-2 text-[11px] text-gray-400 cursor-pointer select-none leading-tight">

                </label>
            </div>
            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-600 to-cyan-500 text-white font-bold py-2.5 rounded-xl text-sm shadow-lg shadow-indigo-600/20 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                Đăng Ký Thành Viên Mới
            </button>
        </form>
        <div class="text-center border-t border-gray-800/80 pt-3">
            <p class="text-xs text-gray-400">Đã là thành viên MANGATIN?<a href="index.php?action=login"
                    class="text-indigo-400 font-semibold hover:underline"> Đăng nhập tại đây</a></p>
        </div>
    </div>

    <script>
    // Lấy các phần tử dựa vào ID đã đặt ở trên
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const togglePasswordButton = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');
    const confirmEyeIcon = document.getElementById('confirmEyeIcon');
    // Lắng nghe sự kiện click vào nút con mắt
    togglePasswordButton.addEventListener('click', function() {
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
    toggleConfirmPasswordButton.addEventListener('click', function() {
        // Kiểm tra loại thuộc tính hiện tại của ô input
        if (confirmPasswordInput.type === 'password') {
            // Nếu đang ẩn (password) -> chuyển sang hiện (text)
            confirmPasswordInput.type = 'text';

            // Đổi icon thành mắt gạch chéo (nếu dùng Font Awesome v6)
            confirmEyeIcon.classList.remove('fa-eye');
            confirmEyeIcon.classList.add('fa-eye-slash');
        } else {
            // Nếu đang hiện (text) -> chuyển về ẩn (password)
            confirmPasswordInput.type = 'password';

            // Đổi icon quay lại mắt bình thường
            confirmEyeIcon.classList.remove('fa-eye-slash');
            confirmEyeIcon.classList.add('fa-eye');
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</body>

</html>