<?php $__env->startSection('content'); ?>
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Banner</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.banners.index')); ?>">Banners</a></li>
                    <li class="breadcrumb-item active">Edit Banner</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('admin.banners.update', $banner->id)); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label>Banner Type <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input banner-type" type="radio" name="type" id="type_content"
                                        value="content_image" <?php echo e($banner->type == 'content_image' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="type_content">
                                        Content with Image (Standard)
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input banner-type" type="radio" name="type" id="type_image"
                                        value="image_only" <?php echo e($banner->type == 'image_only' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="type_image">
                                        Image Only (Full Width)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Banner Image</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="image">
                            <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">Leave empty to keep current image</small>
                            <?php if($banner->image): ?>
                                <div class="mt-2">
                                    <img src="<?php echo e(asset($banner->image)); ?>" alt="Current Banner" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Image Only Link Field -->
                        <div id="image_only_link" class="mb-3" style="display: none;">
                            <label>Banner Link URL</label>
                            <input type="text" class="form-control" name="image_link" value="<?php echo e($banner->button_link); ?>"
                                placeholder="e.g. https://example.com or /search">
                            <small class="form-text text-muted">Enter the URL where the banner image should link to when
                                clicked</small>
                        </div>

                        <!-- Content Fields Wrapper -->
                        <div id="content_fields">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" value="<?php echo e($banner->title); ?>"
                                            placeholder="e.g. The Largest Online Doctor Platform">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Stats Text (Trusted By)</label>
                                        <input type="text" class="form-control" name="stats_text"
                                            value="<?php echo e($banner->stats_text); ?>" placeholder="e.g. 700,000 Patients">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Subtitle / Description</label>
                                <textarea class="form-control" name="subtitle" rows="3"><?php echo e($banner->subtitle); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Button Text</label>
                                        <input type="text" class="form-control" name="button_text"
                                            value="<?php echo e($banner->button_text); ?>" placeholder="e.g. Consult Now">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>Button Link</label>
                                        <input type="text" class="form-control" name="button_link"
                                            value="<?php echo e($banner->button_link); ?>" placeholder="e.g. /search">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Order Priority</label>
                                    <input type="number" class="form-control" name="order" value="<?php echo e($banner->order); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Status</label>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="is_active" class="check" name="is_active" <?php echo e($banner->is_active ? 'checked' : ''); ?>>
                                        <label for="is_active" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Update Banner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            function toggleFields() {
                if ($('#type_image').is(':checked')) {
                    $('#content_fields').hide();
                    $('#image_only_link').show();
                    $('input[name="title"]').prop('required', false);
                } else {
                    $('#content_fields').show();
                    $('#image_only_link').hide();
                    $('input[name="title"]').prop('required', true);
                }
            }

            // Initial check
            toggleFields();

            // On change
            $('input[name="type"]').change(function () {
                toggleFields();
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/admin/banners/edit.blade.php ENDPATH**/ ?>