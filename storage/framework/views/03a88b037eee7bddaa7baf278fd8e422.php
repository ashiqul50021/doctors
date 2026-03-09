<?php $__env->startSection('title', 'Login - ' . ($siteSettings['site_name'] ?? 'Doccure')); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Content -->
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-8 offset-md-2">

                    <!-- Login Tab Content -->
                    <div class="account-content">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-7 col-lg-6 login-left">
                                <img src="<?php echo e(asset('assets/img/login-banner.png')); ?>" class="img-fluid" alt="Doccure Login">
                            </div>
                            <div class="col-md-12 col-lg-6 login-right">
                                <div class="login-header">
                                    <h3>Login <span>Doccure</span></h3>
                                </div>
                                <form action="<?php echo e(route('login.submit')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3 form-focus">
                                        <input type="email" class="form-control floating" name="email" required
                                            value="<?php echo e(old('email')); ?>">
                                        <label class="focus-label">Email</label>
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="mb-3 form-focus">
                                        <input type="password" class="form-control floating" name="password" required>
                                        <label class="focus-label">Password</label>
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger small"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="text-end">
                                        <a class="forgot-link" href="<?php echo e(route('forgot.password')); ?>">Forgot Password ?</a>
                                    </div>
                                    <button class="btn btn-primary btn-block btn-lg login-btn" type="submit">Login</button>
                                    <div class="login-or">
                                        <span class="or-line"></span>
                                        <span class="span-or">or</span>
                                    </div>
                                    <div class="row form-row social-login">
                                        <div class="col-6">
                                            <a href="#" class="btn btn-facebook btn-block"><i
                                                    class="fab fa-facebook-f me-1"></i> Login</a>
                                        </div>
                                        <div class="col-6">
                                            <a href="#" class="btn btn-google btn-block"><i class="fab fa-google me-1"></i>
                                                Login</a>
                                        </div>
                                    </div>
                                    <div class="text-center dont-have">Don't have an account? <a
                                            href="<?php echo e(route('register')); ?>">Register</a></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /Login Tab Content -->

                </div>
            </div>

        </div>
    </div>
    <!-- /Page Content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/frontend/login.blade.php ENDPATH**/ ?>