<?php
require 'config.php';
session_start();

// Función para iniciar sesión
function login($username, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT u.*, t.nombre, t.apellido FROM usuarios u JOIN trabajadores t ON u.trabajador_id = t.id WHERE u.username = ? AND u.activo = TRUE");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'trabajador_id' => $user['trabajador_id'],
            'username' => $user['username'],
            'rol' => $user['rol'],
            'nombre' => $user['nombre'],
            'apellido' => $user['apellido']
        ];
        return true;
    }
    return false;
}

// Función para cerrar sesión
function logout() {
    session_unset();
    session_destroy();
}

// Verifica si el usuario está autenticado
function isAuthenticated() {
    return isset($_SESSION['user']);
}

// Verifica si el usuario tiene un rol específico
function hasRole($role) {
    return isAuthenticated() && $_SESSION['user']['rol'] === $role;
}

// Redirige si no está autenticado
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

// Redirige si no tiene el rol requerido
function requireRole($role) {
    requireAuth();
    if (!hasRole($role)) {
        header('HTTP/1.0 403 Forbidden');
        echo 'Acceso denegado: No tienes permisos suficientes';
        exit;
    }
}

// Genera un token CSRF
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Valida el token CSRF
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
