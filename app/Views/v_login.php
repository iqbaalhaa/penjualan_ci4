<!doctype html>
<html lang="en">
  <head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= base_url('login') ?>/css/style.css">
  </head>
  <body class="img js-fullheight" style="background-image: url(<?= base_url('login') ?>/images/bg.jpg);">
    <div class="overlay"></div>
    <section class="ftco-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-4">
          <br>
          <br>
          <br>
          <br>
          <br>   
		  <div class="login-wrap p-0">
              <h3 class="mb-4 text-center"><b>Masuk</b></h3>

              <!-- Flash Message (Jika Login Gagal) -->
              <?php if (session()->getFlashdata('msg')): ?>
                <div class="alert alert-danger">
                  <?= session()->getFlashdata('msg'); ?>
                </div>
              <?php endif; ?>

              <!-- Form Login -->
              <form action="<?= base_url('Auth/login'); ?>" method="POST" class="signin-form">
                <?= csrf_field(); ?>  <!-- Tambahkan CSRF Token -->
                
                <div class="form-group">
                  <input type="text" name="username" class="form-control" placeholder="Nama Pengguna" required>
                </div>
                
                <div class="form-group">
                  <input id="password-field" type="password" name="password" class="form-control" placeholder="Kata Sandi" required>
                  <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                </div>

                <div class="form-group">
                  <button type="submit" class="form-control btn btn-primary submit px-3"><b>Masuk</b></button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </section>

    <script src="<?= base_url('login') ?>/js/jquery.min.js"></script>
    <script src="<?= base_url('login') ?>/js/popper.js"></script>
    <script src="<?= base_url('login') ?>/js/bootstrap.min.js"></script>
    <script src="<?= base_url('login') ?>/js/main.js"></script>
  </body>
</html>