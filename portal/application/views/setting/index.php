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
  <!-- DataTables -->
  <link rel="stylesheet" href="<?=$link;?>/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=$link;?>/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?=$link;?>/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?=$link;?>/plugins/toastr/toastr.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?=$link;?>/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
</head>
<!-- dark-mode -->
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
          <a class="nav-link" href="<?=base_url();?>" role="button" title='Portal'>
            <i class="fas fa-home"></i>
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
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Setting Portal</h1>
          </div>
          <!-- <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Layout</a></li>
              <li class="breadcrumb-item active">Top Navigation</li>
            </ol>
          </div> -->
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h5 class="card-title m-0 title-add">Add Portal</h5>
          </div>
          <form action="#" method="POST" id="form_portal" autocomplete='off'> 
            <div class="card-body">
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Portal Name</label>
                    <input type="text" id='name' name='name' class="form-control" placeholder="Portal name">
                    <input type="hidden" id='id' name='id' class="form-control" placeholder="Portal name">
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Link</label>
                    <input type="text" id='link' name='link' class="form-control" placeholder="Link portal">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" id='desc' name='desc' rows="3" placeholder="Description"></textarea>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <label>Sort</label>
                    <input type='text' class="form-control maskMoney" id='sort' name='sort' rows="3" placeholder="Sort" data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="button" class="btn btn-primary" id='save'>Save</button>
              <button type="button" class="btn btn-danger" id='reset'>Reset</button>
            </div>
          </form>
        </div>

        <div class="card card-primary card-outline">
          <div class="card-header">
            <h5 class="card-title m-0">List Portal</h5>
          </div>
          <div class="card-body">
            <table id="list_portal" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Portal Name</th>
                  <th>Link</th>
                  <th>Description</th>
                  <th>Order</th>
                  <th>Option</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach($portal AS $val => $value){ $val++;
                ?>
                  <tr>
                    <td><?=$val;?></td>
                    <td><?=ucwords(strtolower($value['name']));?></td>
                    <td><?=$value['link'];?></td>
                    <td><?=ucfirst(strtolower($value['desc']));?></td>
                    <td><?=$value['sort'];?></td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info">Action</button>
                        <button type="button" class="btn btn-info dropdown-toggle dropdown-icon" data-toggle="dropdown">
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu" role="menu">
                          <a class="dropdown-item text-green edit" data-id='<?=$value['id'];?>'><i class='fa fa-edit'></i>&nbsp;Edit</a>
                          <a class="dropdown-item text-red delete" data-id='<?=$value['id'];?>'><i class='fa fa-trash'></i>&nbsp;&nbsp;Delete</a>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>
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
<!-- DataTables  & Plugins -->
<script src="<?=$link;?>/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?=$link;?>/plugins/jszip/jszip.min.js"></script>
<script src="<?=$link;?>/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?=$link;?>/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?=$link;?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?=$link;?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Toastr -->
<script src="<?=$link;?>/plugins/toastr/toastr.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?=$link;?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Mask Money -->
<script src="<?=$link_g;?>/general/jquery.maskMoney.min.js"></script>
</body>
</html>

<style>
	a.disabled {
		pointer-events: none;
		cursor: pointer;
	}
  .dropdown-item{
    cursor: pointer;
  }
</style>
<script>
  var base_url			    = '<?php echo base_url(); ?>';
  var active_controller	= '<?php echo($this->uri->segment(1)); ?>';
  $(document).ready(function(){
    $('#list_portal').DataTable({
      "paging": true,
      "stateSave" : true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": true,
    });

    $('.maskMoney').maskMoney();

    //Delete
    $(document).on('click', '.delete', function(){
			var bF	= $(this).data('id');
			const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
      },
      buttonsStyling: false
      })
      let timerInterval;
      swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: 'Yes, process it!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
          }).then((result) => {
          if (result.value) {
              
              var baseurl = base_url + active_controller+'/delete/'+bF;
              $.ajax({
              url			: baseurl,
              type		: "POST",
              cache		: false,
              dataType	: 'json',
              processData	: false,
              contentType	: false,
              success		: function(data){
                  if(data.status == 1){
                  swalWithBootstrapButtons.fire({
                      title	: "Save Success!",
                      text	: data.pesan,
                      icon	: "success",
                      timer	: 1000,
                      timerProgressBar: true,
                      onClose: () => {
                          clearInterval(timerInterval);
                          window.location.href = base_url + active_controller;
                      }
                      });
                  }
                  else{
                  swalWithBootstrapButtons.fire({
                      title	: "Save Failed!",
                      text	: data.pesan,
                      icon	: "warning",
                      timer	: 5000,
                      showCancelButton	: false,
                      showConfirmButton	: false,
                      allowOutsideClick	: false
                  });
                  }
              },
              error: function() {
                  swalWithBootstrapButtons.fire({
                  title				: "Error Message !",
                  text				: 'An Error Occured During Process. Please try again..',
                  icon				: "warning",
                  timer				: 5000,
                  showCancelButton	: false,
                  showConfirmButton	: false,
                  allowOutsideClick	: false
                  });
              }
              });
          } else if (
              result.dismiss === Swal.DismissReason.cancel
          ) {
              swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data can be process again :)',
              'error'
              )
          }
      })
		});

    //Edit
    //Delete
    $(document).on('click', '.edit', function(){
			var bF	= $(this).data('id');
			var baseurl = base_url + active_controller+'/edit/'+bF;
      $.ajax({
      url			: baseurl,
      type		: "POST",
      cache		: false,
      dataType	: 'json',
      processData	: false,
      contentType	: false,
      success		: function(data){
          if(data.status == 1){
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#link').val(data.link);
            $('#desc').val(data.desc);
            $('#sort').val(data.sort);
            $("html, body").animate({ scrollTop: 0 }, 600);
            $('.title-add').html('Edit Portal');
            $('#save').html('Update');
          }
          else{
          swalWithBootstrapButtons.fire({
              title	: "Save Failed!",
              text	: data.pesan,
              icon	: "warning",
              timer	: 5000,
              showCancelButton	: false,
              showConfirmButton	: false,
              allowOutsideClick	: false
          });
          }
      },
      error: function() {
          swalWithBootstrapButtons.fire({
          title				: "Error Message !",
          text				: 'An Error Occured During Process. Please try again..',
          icon				: "warning",
          timer				: 5000,
          showCancelButton	: false,
          showConfirmButton	: false,
          allowOutsideClick	: false
          });
      }
      });
		});

    //Save
    $(document).on('click', '#save', function(){
			$(this).prop('disabled',true);
			var name	= $('#name').val();
      var link	= $('#link').val();
			
			if(name==''){
				toastr.warning('EMPTY PORTAL NAME !');
				$(this).prop('disabled',false);
				return false;
			}

      // if(link==''){
			// 	toastr.info('Empty portal link ...');
			// 	$(this).prop('disabled',false);
			// 	return false;
			// }

			const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
      },
      buttonsStyling: false
      })
      let timerInterval;
      swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: 'Yes, process it!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
          }).then((result) => {
          if (result.value) {
              var formData 	=new FormData($('#form_portal')[0]);
              var baseurl = base_url + active_controller+'/save';
              $.ajax({
              url			: baseurl,
              type		: "POST",
              data		: formData,
              cache		: false,
              dataType	: 'json',
              processData	: false,
              contentType	: false,
              success		: function(data){
                  if(data.status == 1){
                  swalWithBootstrapButtons.fire({
                      title	: "Save Success!",
                      text	: data.pesan,
                      icon	: "success",
                      timer	: 1000,
                      timerProgressBar: true,
                      onClose: () => {
                          clearInterval(timerInterval);
                          window.location.href = base_url + active_controller;
                      }
                      });
                  }
                  else{
                  swalWithBootstrapButtons.fire({
                      title	: "Save Failed!",
                      text	: data.pesan,
                      icon	: "warning",
                      timer	: 5000,
                      showCancelButton	: false,
                      showConfirmButton	: false,
                      allowOutsideClick	: false
                  });
                  }
              },
              error: function() {
                  swalWithBootstrapButtons.fire({
                  title				: "Error Message !",
                  text				: 'An Error Occured During Process. Please try again..',
                  icon				: "warning",
                  timer				: 5000,
                  showCancelButton	: false,
                  showConfirmButton	: false,
                  allowOutsideClick	: false
                  });
              }
              });
          } else if (
              result.dismiss === Swal.DismissReason.cancel
          ) {
              swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data can be process again :)',
              'error'
              )
          }
      })
		});

    //Reset
    $(document).on('click', '#reset', function(){
      $('#id').val('');
      $('#name').val('');
      $('#link').val('');
      $('#desc').val('');
      $('#sort').val('');
      $('.title-add').html('Add Portal');
      $('#save').html('Save');
    });

  });
</script>