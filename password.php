<?php
    //para validar si se inicio sesion
    session_start(); 
    if(!isset($_SESSION['idcliente']))
      header("Location: login.html");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laura Arcangel - Cursos en venta</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  
  <!-- ***** CSS necesarios de acuerdo al contenido ***** -->
  <!-- *****  ***** -->

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
    </div>

    <?php
        //Barra de navegación
        include '_nav.php';
        //Menu lateral
        include '_sidebar.php';
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">CURSOS EN VENTA</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="index.php">INICIO</a></li>
                <li class="breadcrumb-item active">Contraseña</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        
        
        <!-- Default box -->
        <div class="card card-solid">
          <div class="card-body pb-0">
            
            <div class="login-box">
              <div class="card card-outline card-primary">
                <div class="card-header text-center">
                  <a class="h1"> </a>
                </div>
                <div class="card-body">
                  <form id="FrmPass" method="post">
                    <div class="form-group">
                      <label for="TxtPass1">Escribe tu nueva contraseña.</label>
                      <div class="input-group mb-3">
                        <input id="TxtPass1" type="password" class="form-control" placeholder="Nueva contraseña">
                        <div class="input-group-append">
                          <div class="input-group-text">
                            <span class="fa-solid fa-key"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="TxtPass2">Confirma tu nueva contraseña.</label>
                      <div class="input-group mb-3">
                        <input id="TxtPass2" type="password" class="form-control" placeholder="Confirma tu contraseña">
                        <div class="input-group-append">
                          <div class="input-group-text">
                            <span class="fa-solid fa-key"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Cambiar contraseña</button>
                      </div>
                      <!-- /.col -->
                    </div>
                  </form>
                </div>
                <!-- /.login-card-body -->
              </div>
            </div>

          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <nav aria-label="Contacts Page Navigation">
              
            </nav>
          </div>
          <!-- /.card-footer -->
        </div>
        <!-- /.card -->


      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <?php
      //Pie de la pagina
      include '_pie.php';
    ?>
    
    

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>

  <!-- ***** JS necesarios de acuerdo al contenido ***** -->
  <!-- SweetAlert2 -->
  <script src="plugins/sweetalert2/sweetalert2.min.js"></script>
  
  <script src="js/password.js"></script>

  
  

</body>
</html>
