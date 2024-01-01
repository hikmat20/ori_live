<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Portal</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?=$link;?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=$link;?>/dist/css/adminlte.min.css">
</head>
<body class="hold-transition layout-top-nav layout-navbar-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-dark navbar-dark">
    <div class="container">
      <a href="<?=base_url();?>" class="navbar-brand">
        <img src="<?=$link;?>/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">PORTAL</span>
      </a>

      <!-- <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button> -->

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->

        <!-- SEARCH FORM -->

      </div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- Messages Dropdown Menu -->
        
        <!-- Notifications Dropdown Menu -->
        
		<li class="nav-item">
			<a class="nav-link" href="<?=base_url('setting');?>" role="button" title='Setting Portal'>
				<i class="fas fa-cogs"></i>
			</a>
		</li>
        <!-- <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
          </a>
        </li> -->
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <!-- <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"> Top Navigation <small>Example 3.0</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Layout</a></li>
              <li class="breadcrumb-item active">Top Navigation</li>
            </ol>
          </div>
        </div>
      </div> -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
		<div class="container">
			<div class="row">
				<?php
				foreach($portal AS $val => $value){
					$disabled_button = (empty($value['link']))?'disabled':'';
					$color_button = (!empty($value['button_color']))?$value['button_color']:'primary';
				?>
				<div class="col-lg-4">
					<div class="card card-<?=$color_button;?> card-outline">
						<div class="card-header">
							<h5 class="card-title m-0"><?=strtoupper($value['name']);?></h5>
						</div>
						<div class="card-body">
							<!-- <h6 class="card-title">Special title treatment</h6> -->

							<p class="card-text"><?=ucfirst(strtolower($value['desc']));?></p>
							<a href="<?=$value['link'];?>" class="btn btn-sm btn-<?=$color_button;?> <?=$disabled_button;?>" target='_blank'><?=ucwords(strtolower($value['button_title']));?></a>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
    </div>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer navbar-dark">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
		Portal
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?=date('Y');?> ORI GROUP</strong>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="<?=$link;?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?=$link;?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=$link;?>/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?=$link;?>/dist/js/demo.js"></script>
</body>
</html>

<style>
	a.disabled {
		pointer-events: none;
		cursor: default;
	}
</style>