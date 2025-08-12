<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CaterBid - Dashboard <?php echo ucfirst($usuario['rol_nombre']); ?></title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../recursos/css/admin.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon">
                    <img src="../recursos/images/logo.jpeg" alt="Logo" style="width: 30px; height: 30px; border-radius: 6px;">
                </div>
                <div class="sidebar-brand-text mx-3">CaterBid</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">Principal</div>

            <!-- Menú dinámico se carga aquí via JavaScript -->

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Mi Perfil -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-file="perfil.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Mi Perfil</span>
                </a>
            </li>

            <!-- Nav Item - Ayuda -->
            <li class="nav-item">
                <a class="nav-link" href="#" data-file="ayuda.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>Ayuda</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Buscar servicios, empresas..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                                </span>
                                <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <h6 class="dropdown-header">
                                    <?php echo htmlspecialchars($usuario['nombre']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($usuario['correo']); ?></small><br>
                                    <small class="text-muted"><?php echo ucfirst($usuario['rol_nombre']); ?></small>
                                </h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-file="perfil.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <a class="dropdown-item" href="#" data-file="configuracion.php">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configuración
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../config/logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Área de contenido dinámico -->
                    <main id="content-area">
                        <!-- El contenido se carga dinámicamente aquí -->
                    </main>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; CaterBid 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Selecciona "Cerrar Sesión" si estás listo para terminar tu sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="config/logout.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <script>
    $(document).ready(function() {
        console.log('Dashboard cargado');
        
        // Cargar datos del usuario y menú dinámico
        $.ajax({
            url: 'obtener_datos_usuario.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Datos recibidos:', data);
                
                if (data.error) {
                    window.location.href = 'index.html';
                    return;
                }

                // Encontrar el elemento heading "Principal"
                const principalHeading = $('.sidebar-heading:contains("Principal")');
                
                if (principalHeading.length && data.permisos) {
                    // Insertar elementos dinámicos después del heading "Principal"
                    data.permisos.forEach(function(p) {
                        const menuItem = `
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-file="${p.file}">
                                    <i class="${p.icon}"></i>
                                    <span>${p.title}</span>
                                </a>
                            </li>
                        `;
                        principalHeading.after(menuItem);
                    });
                }

                
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar datos del usuario:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                // No redirigir inmediatamente para poder debuggear
                // window.location.href = 'index.html';
            }
        });

        // Manejo de clicks para cargar contenido
        $(document).on('click', 'a[data-file]', function(e) {
            e.preventDefault();
            const file = $(this).attr('data-file');
            
            // Remover clase active de todos los nav-items
            $('.nav-item').removeClass('active');
            
            // Agregar clase active al nav-item clickeado
            $(this).closest('.nav-item').addClass('active');
            
            // Cargar contenido
            loadContent(file);
        });

        // Función para cargar contenido
        function loadContent(file) {
            console.log('Cargando archivo:', file);
            $.ajax({
                url: file,
                type: 'GET',
                success: function(html) {
                    $('#content-area').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar contenido:', error);
                    $('#content-area').html('<div class="alert alert-danger">Error al cargar el contenido: ' + file + '</div>');
                }
            });
        }

        // Sidebar toggle functionality
        $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
            $("body").toggleClass("sidebar-toggled");
            $(".sidebar").toggleClass("toggled");
            if ($(".sidebar").hasClass("toggled")) {
                $('.sidebar .collapse').collapse('hide');
            };
        });

        // Close any open menu accordions when window is resized below 768px
        $(window).resize(function() {
            if ($(window).width() < 768) {
                $('.sidebar .collapse').collapse('hide');
            };
            
            // Toggle the side navigation when window is resized below 480px
            if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
                $("body").addClass("sidebar-toggled");
                $(".sidebar").addClass("toggled");
                $('.sidebar .collapse').collapse('hide');
            };
        });

        // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
        $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
            if ($(window).width() > 768) {
                var e0 = e.originalEvent,
                    delta = e0.wheelDelta || -e0.detail;
                this.scrollTop += (delta < 0 ? 1 : -1) * 30;
                e.preventDefault();
            }
        });

        // Scroll to top button appear
        $(document).on('scroll', function() {
            var scrollDistance = $(this).scrollTop();
            if (scrollDistance > 100) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });

        // Smooth scrolling using jQuery easing
        $(document).on('click', 'a.scroll-to-top', function(e) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: ($($anchor.attr('href')).offset().top)
            }, 1000, 'easeInOutExpo');
            e.preventDefault();
        });
    });
    </script>

</body>

</html>