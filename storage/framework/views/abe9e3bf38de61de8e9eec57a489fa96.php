<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title><?php echo $__env->yieldContent('title', 'Admin - ' . ($siteSettings['site_name'] ?? 'abcsheba.com')); ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon.png')); ?>">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">



    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

    <?php echo $__env->yieldPushContent('styles'); ?>

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('backend/css/style.css')); ?>">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('backend/css/admin-custom.css')); ?>">
</head>

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <?php echo $__env->make('admin.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <div class="content container-fluid">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="<?php echo e(asset('assets/js/jquery.min.js')); ?>"></script>

    <!-- Bootstrap Core JS -->
    <script src="<?php echo e(asset('assets/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.min.js')); ?>"></script>

    <!-- Slimscroll JS -->
    <script src="<?php echo e(asset('backend/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        <?php if(session('success')): ?>
            toastr.success("<?php echo e(session('success')); ?>");
        <?php endif; ?>
        <?php if(session('error')): ?>
            toastr.error("<?php echo e(session('error')); ?>");
        <?php endif; ?>
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Custom JS -->
    <script src="<?php echo e(asset('backend/js/script.js')); ?>"></script>
</body>

</html><?php /**PATH /home/ashiqul/.valet/Sites/doctor/resources/views/layouts/admin.blade.php ENDPATH**/ ?>