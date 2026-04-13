<?php
include('../config/database.php'); 
session_start();

$errors = [];
$username = '';
$name = '';
$email = '';
$password = '';
$confirm_password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; 

    // 1. Kiểm tra dữ liệu input (Không dấu, không khoảng trắng)
    $errors_chars = '/[áàảãạăắằẳẵặâấầẩẫậéèẻẽẹêếềểễệíìỉĩịóòỏõọôốồổỗộơớờởỡợúùủũụưứừửữựýỳỷỹỵđ\s]/i';
    if (preg_match($errors_chars, $username)) {
        $errors['username'] = 'Tên đăng nhập không được chứa dấu hoặc khoảng trắng!';
    }
    
    // 2. Kiểm tra mật khẩu
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Mật khẩu không trùng khớp!';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
    }

    // 3. Kiểm tra tồn tại tên đăng nhập hoặc email
    if (empty($errors)) {
        $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $hashedUser = md5($username); // Giữ logic hash username của bạn
        $stmt_check->bind_param("ss", $hashedUser, $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $errors['exists'] = 'Tên đăng nhập hoặc email đã tồn tại!';
        }
        $stmt_check->close();
    }

    // 4. Thực hiện lưu vào Database (ĐĂNG KÝ LUÔN - KHÔNG OTP)
    if (empty($errors)) {
        $hashedUsername = md5($username);        
        $hashedPassword = md5($password);
        $sql = "INSERT INTO users (username, password, name, email, roles) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql);
        $stmt_insert->bind_param("sssss", $hashedUsername, $hashedPassword, $name, $email, $role);
        
        if ($stmt_insert->execute()) {
            echo "<script>alert('Đăng ký thành công!'); window.location.href='login.php';</script>";
            exit(); 
        } else {
            $errors['database'] = 'Lỗi hệ thống: ' . $conn->error;
        }
        $stmt_insert->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../img/logo.png" rel="icon">
    <title>Đăng Ký - TRUYENTRANHNET</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <?php include('../includes/header.php'); ?>

    <main class="container mx-auto px-4 py-8 pt-16 flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full bg-gray-800 p-6 rounded-lg shadow-lg">
            <h1 class="text-center text-3xl font-bold mb-4">Đăng Ký</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-sm"><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm mb-1">Tên Đăng Nhập</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required
                           class="w-full p-2 bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm mb-1">Họ và Tên</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required
                           class="w-full p-2 bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm mb-1">Địa Chỉ Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required
                           class="w-full p-2 bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm mb-1">Mật khẩu</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full p-2 bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1">Xác Nhận Mật Khẩu</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full p-2 bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" name="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition font-bold">
                    Đăng Ký
                </button>
                
                <div class="text-center text-sm text-gray-400 mt-4">
                    Đã có tài khoản? <a href="login.php" class="text-blue-400 hover:underline">Đăng nhập</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>