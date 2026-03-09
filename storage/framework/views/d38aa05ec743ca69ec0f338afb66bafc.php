<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Banners</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Banners</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="<?php echo e(route('admin.banners.create')); ?>" class="btn btn-primary float-end mt-2">Add Banner</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($banner->order); ?></td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="<?php echo e(asset($banner->image)); ?>" class="avatar avatar-sm me-2" target="_blank">
                                            <img class="avatar-img rounded-circle" src="<?php echo e(asset($banner->image)); ?>" alt="Banner Image">
                                        </a>
                                    </h2>
                                </td>
                                <td>
                                    <?php if($banner->type == 'content_image'): ?>
                                        <span class="badge badge-info">Content + Image</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Image Only</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($banner->title ?? '-'); ?></td>
                                <td>
                                    <?php if($banner->is_active): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="<?php echo e(route('admin.banners.edit', $banner->id)); ?>">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="<?php echo e(route('admin.banners.destroy', $banner->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm bg-danger-light">
                                                <i class="fe fe-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/admin/banners/index.blade.php ENDPATH**/ ?>