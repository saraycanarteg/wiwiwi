<?php
/**
 * Función para verificar si el usuario tiene un permiso específico
 * @param string $permiso_requerido - Nombre del permiso a verificar
 * @return bool - True si tiene permiso, False si no
 */
function verificarPermiso($permiso_requerido) {
    // Verificar si hay sesión activa
    if (!isset($_SESSION['usuario'])) {
        return false;
    }
    
    $usuario = $_SESSION['usuario'];
    
    // Si es administrador, tiene todos los permisos
    if (isset($usuario['rol_nombre']) && strtolower($usuario['rol_nombre']) === 'administrador') {
        return true;
    }
    
    // Verificar si tiene el permiso específico
    if (isset($usuario['permisos']) && is_array($usuario['permisos'])) {
        return in_array($permiso_requerido, $usuario['permisos']);
    }
    
    return false;
}

/**
 * Función para verificar múltiples permisos (OR lógico)
 * @param array $permisos_requeridos - Array de permisos a verificar
 * @return bool - True si tiene al menos uno de los permisos
 */
function verificarCualquierPermiso($permisos_requeridos) {
    foreach ($permisos_requeridos as $permiso) {
        if (verificarPermiso($permiso)) {
            return true;
        }
    }
    return false;
}

/**
 * Función para verificar todos los permisos (AND lógico)
 * @param array $permisos_requeridos - Array de permisos a verificar
 * @return bool - True si tiene todos los permisos
 */
function verificarTodosLosPermisos($permisos_requeridos) {
    foreach ($permisos_requeridos as $permiso) {
        if (!verificarPermiso($permiso)) {
            return false;
        }
    }
    return true;
}

/**
 * Redirigir si no tiene permiso
 * @param string $permiso_requerido
 * @param string $redirect_url - URL de redirección (por defecto dashboard)
 */
function requierePermiso($permiso_requerido, $redirect_url = '../../includes/dashboard.php') {
    if (!verificarPermiso($permiso_requerido)) {
        header("Location: " . $redirect_url);
        exit();
    }
}

/**
 * Redirigir si no tiene ninguno de los permisos
 * @param array $permisos_requeridos
 * @param string $redirect_url
 */
function requiereCualquierPermiso($permisos_requeridos, $redirect_url = '../../includes/dashboard.php') {
    if (!verificarCualquierPermiso($permisos_requeridos)) {
        header("Location: " . $redirect_url);
        exit();
    }
}
?>