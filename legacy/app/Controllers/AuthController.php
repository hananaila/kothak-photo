<?php
// File: app/Controllers/AuthController.php
// Tugas 2.1: Sistem Autentikasi (Register & Login) untuk Pelanggan, Kasir, dan Admin

declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';

class AuthController {
    public function register(array $input): array {
        $pdo   = Database::getConnection();
        $name  = trim($input['name'] ?? '');
        $email = strtolower(trim($input['email'] ?? ''));
        $phone = trim($input['phone_whatsapp'] ?? '');
        $pass  = $input['password'] ?? '';
        $role  = in_array($input['role'] ?? 'customer', ['customer', 'cashier', 'admin'], true)
                 ? $input['role'] : 'customer';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
            return ['success' => false, 'message' => 'Data registrasi belum lengkap atau password kurang dari 6 karakter.'];
        }

        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmtCheck->execute(['email' => $email]);
        if ($stmtCheck->fetch()) {
            return ['success' => false, 'message' => 'Email sudah terdaftar. Silakan gunakan halaman Login.'];
        }

        $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);

        $stmtInsert = $pdo->prepare("
            INSERT INTO users (name, email, phone_whatsapp, password, role)
            VALUES (:name, :email, :phone, :password, :role)
        ");
        $stmtInsert->execute([
            'name'     => $name,
            'email'    => $email,
            'phone'    => $phone,
            'password' => $hashedPassword,
            'role'     => $role,
        ]);

        $userId = (int) $pdo->lastInsertId();
        $_SESSION['user'] = [
            'id'             => $userId,
            'name'           => $name,
            'email'          => $email,
            'phone_whatsapp' => $phone,
            'role'           => $role,
        ];

        return ['success' => true, 'redirect' => $this->resolveRedirectByRole($role)];
    }

    public function login(string $email, string $password): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, name, email, phone_whatsapp, password, role FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => strtolower(trim($email))]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email atau kata sandi tidak sesuai.'];
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'             => (int) $user['id'],
            'name'           => $user['name'],
            'email'          => $user['email'],
            'phone_whatsapp' => $user['phone_whatsapp'],
            'role'           => $user['role'],
        ];

        return [
            'success'  => true,
            'role'     => $user['role'],
            'redirect' => $this->resolveRedirectByRole($user['role']),
        ];
    }

    private function resolveRedirectByRole(string $role): string {
        return match ($role) {
            'admin'   => '/admin/dashboard.php',
            'cashier' => '/cashier/dashboard.php',
            default   => '/customer/select_schedule.php',
        };
    }
}
