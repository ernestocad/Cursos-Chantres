<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="Laura Arcangel" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Laura Arcangel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <!-- <div class="image">
          <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div> -->
        <div class="info">
          <?php
            //$nombre = $_SESSION['Nombre'];
          ?>
          <a href="" class="d-block"><?php //echo $_SESSION['Nombre'];?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fa-regular fa-rectangle-list"></i>
              <p>Mis cursos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="cursos-venta.php" class="nav-link">
              <i class="nav-icon fa-solid fa-money-check-dollar"></i>
              <p>Cursos en venta</p>
            </a>
          </li>

          <div class="user-panel mt-1 pb-1 mb-1 d-flex">
          </div>

          <li class="nav-header">CONFIGURACIÓN</li>
          <li class="nav-item">
            <a href="password.php" class="nav-link">
              <i class="nav-icon fa-solid fa-user-lock"></i>
              <p>
                Contraseña
              </p>
            </a>
          </li>
          

          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  