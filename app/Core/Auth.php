<?php

namespace App\Core;

class Auth
{
    public static function login($user)
    {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
    }

    public static function logout()
    {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }

    public static function check()
    {
        return isset($_SESSION['user_id']);
    }

    public static function guest()
    {
        return !self::check();
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }

    public static function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role()
    {
        return $_SESSION['user_role'] ?? null;
    }

    public static function isPatient()
    {
        return self::role() === 'patient';
    }

    public static function isDoctor()
    {
        return self::role() === 'doctor';
    }

    public static function isAdmin()
    {
        return self::role() === 'admin';
    }

    public static function requireAuth()
    {
        if (self::guest()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            exit;
        }
    }

    public static function requireRole($role)
    {
        self::requireAuth();
        
        if (self::role() !== $role) {
            http_response_code(403);
            die("403 - Forbidden: You don't have permission to access this page");
        }
    }

    public static function requireRoles($roles)
    {
        self::requireAuth();
        
        if (!in_array(self::role(), $roles)) {
            http_response_code(403);
            die("403 - Forbidden: You don't have permission to access this page");
        }
    }

    public static function intended($default = '/')
    {
        $url = $_SESSION['intended_url'] ?? $default;
        unset($_SESSION['intended_url']);
        return $url;
    }

    public static function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}

?>