<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit();
}

require_once '../config/database.php';

// Verificar conexión
if (!isset($conn) || $conn->connect_error) {
    echo '<div class="alert alert-danger">Error de conexión a la base de datos</div>';
    exit();
}

// Debug de sesión
error_log("Contenido de sesión: " . print_r($_SESSION, true));

// Obtener el ID del usuario de la sesión
$usuario_id = intval($_SESSION['usuario_id']); 

try {
    // Obtener datos del usuario por ID
    $stmt = $conn->prepare("
        SELECT 
            id_usuario,
            nombre,
            correo,
            direccion
        FROM usuario 
        WHERE id_usuario = ?
    ");
    
    error_log("Buscando usuario con ID: " . $usuario_id);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($usuario = $result->fetch_assoc()) {
        ?>
        <!-- Incluir CSS personalizado -->
        <link rel="stylesheet" href="../recursos/css/perfil.css">
        
        <div class="container-fluid perfil-container">
            <div class="row justify-content-center align-items-center mb-4">
                <div class="col-auto">
                    <div class="profile-icon">
                        <i class="fas fa-user-circle fa-3x text-primary"></i>
                    </div>
                </div>
                <div class="col-auto">
                    <h1 class="h3 mb-0 text-gray-800">
                        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                        Mi Perfil
                    </h1>
                    <small class="text-muted">&nbsp&nbsp&nbsp&nbsp&nbsp
                        Gestiona tu información personal</small>
                </div>
            </div>
            
            <!-- Layout en una sola columna -->
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    
                    <!-- Información Personal -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user mr-2"></i>
                                Información Personal
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="formPerfil">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Nombre
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-user text-custom"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                                   value="<?php echo htmlspecialchars($usuario['nombre']); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Correo
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-at text-custom"></i>
                                                </span>
                                            </div>
                                            <input type="email" class="form-control" id="correo" name="correo" 
                                                   value="<?php echo htmlspecialchars($usuario['correo']); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Dirección
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-home text-custom"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                                   value="<?php echo htmlspecialchars($usuario['direccion']); ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Cambiar Contraseña -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 id="primary_label" class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Cambiar Contraseña
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info alert-sm">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Consejo de seguridad:</strong> Usa una contraseña con al menos 6 caracteres, incluyendo números y letras.
                            </div>
                            <form id="formPassword">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Contraseña Actual
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-key text-custom"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" id="password_actual" name="password_actual" 
                                                   placeholder="Ingresa tu contraseña actual">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_actual')">
                                                    <i class="fas fa-eye" id="eye_actual"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Nueva Contraseña
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock text-custom"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" id="password_nueva" name="password_nueva" 
                                                   placeholder="Ingresa la nueva contraseña">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_nueva')">
                                                    <i class="fas fa-eye" id="eye_nueva"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="password-strength mt-1" id="password_strength"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">
                                        Confirmar Nueva
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-lock text-custom"></i>
                                                </span>
                                            </div>
                                            <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" 
                                                   placeholder="Confirma la nueva contraseña">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmar')">
                                                    <i class="fas fa-eye" id="eye_confirmar"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="password-match mt-1" id="password_match"></div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" id="cambio_contr" class="btn btn-primary btn-lg">
                                            <i class="fas fa-key mr-2"></i>
                                            Cambiar Contraseña
                                        </button>
                                        <button type="button" class="btn btn-secondary ml-2" onclick="clearPasswordForm()">
                                            <i class="fas fa-times mr-1"></i>
                                            Limpiar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error al cargar los datos del perfil: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

$conn->close();
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../recursos/js/perfil.js"></script>   