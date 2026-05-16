<?php
/**
 * SEGURANÇA - Config de CSRF e Upload
 */

// ===== PROTEÇÃO CSRF =====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gerar token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Função para gerar campo CSRF
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

// Função para verificar token CSRF
function verify_csrf($token = null) {
    $token = $token ?? $_POST['csrf_token'] ?? '';
    if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        die('❌ Erro de segurança: Token CSRF inválido');
    }
}

// ===== VALIDAÇÃO DE UPLOAD =====
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
]);

function validate_upload($file) {
    if (!isset($file) || $file['error'] !== 0) {
        throw new Exception('Erro no upload do arquivo');
    }

    // Verificar tamanho
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        throw new Exception('Arquivo muito grande (máximo 5MB)');
    }

    // Verificar MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_MIME_TYPES)) {
        throw new Exception('Tipo de arquivo não permitido. Use: JPG, PNG, GIF ou WEBP');
    }

    return $file;
}

// ===== SANITIZAÇÃO =====
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitize_output($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}
?>
