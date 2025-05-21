<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=$judul?> | Sistem Informasi Penjualan </title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/typicons/typicons.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url('backend')?>/assets/js/select.dataTables.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?=base_url('backend')?>/assets/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="<?=base_url('backend')?>/assets/images/favicon.png" />
  </head>
  <body class="with-welcome-text">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="<?=base_url('User')?>">
              <span style="font-size: 24px; font-weight: bold; color:rgb(25, 25, 216);">MS Store</span>
            </a>
            <a class="navbar-brand brand-logo-mini" href="<?=base_url('Admin')?>">
              <span style="font-size: 20px; font-weight: bold; color: #2196f3;">MS Store</span>
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
              <h1 class="welcome-text">Selamat Datang, <span class="text-black fw-bold"><?= session()->get('nama_user'); ?></span></h1>
              <h3 class="welcome-sub-text">Semangat!!!</h3>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="<?=base_url('backend')?>/assets/images/Admin.png" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="<?=base_url('backend')?>/assets/images/Admin.png" alt="Profile image">
                  <p class="mb-1 mt-3 fw-semibold"><?= session()->get('nama_user'); ?></p>
                </div>
                <a class="dropdown-item" href="<?= base_url('Auth/logout'); ?>"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Logout</a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="<?=base_url('User')?>">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            <li class="nav-item nav-category">Menu</li>
            <li class="nav-item">
              <a class="nav-link" href="<?=base_url('User/Produk')?>">
                <i class="menu-icon mdi mdi-store-plus"></i>
                <span class="menu-title">Produk</span>
              </a>
            <li class="nav-item">
              <a class="nav-link" href="<?=base_url('User/Transaksi')?>">
                <i class="menu-icon mdi mdi-finance"></i>
                <span class="menu-title">Transaksi</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row">
              <div class="col-sm-12">
                <div class="home-tab">
                  <div class="tab-content tab-content-basic">

                  <!-- Untuk Mengisi Page -->
                    <?php if (isset($page) && $page) { ?>
                    <?= view($page) ?>
                    <?php } ?>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Sistem Informasi Penjualan <b>MS Store</b></span>
              <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2025. Ahmad Muridan.</span>
            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?=base_url('backend')?>/assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?=base_url('backend')?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="<?=base_url('backend')?>/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="<?=base_url('backend')?>/assets/vendors/progressbar.js/progressbar.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?=base_url('backend')?>/assets/js/off-canvas.js"></script>
    <script src="<?=base_url('backend')?>/assets/js/template.js"></script>
    <script src="<?=base_url('backend')?>/assets/js/settings.js"></script>
    <script src="<?=base_url('backend')?>/assets/js/hoverable-collapse.js"></script>
    <script src="<?=base_url('backend')?>/assets/js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="<?=base_url('backend')?>/assets/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="<?=base_url('backend')?>/assets/js/dashboard.js"></script>
    <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
  </body>
</html>