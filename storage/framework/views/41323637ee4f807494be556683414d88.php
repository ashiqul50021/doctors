<?php $__env->startSection('title', 'Products - Doccure Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row">
        <div class="col-sm-7 col-auto">
            <h3 class="page-title">Products</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ul>
        </div>
        <div class="col-sm-5 col">
            <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary float-right mt-2">Add Product</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <?php if(session('success')): ?>
                    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#PRO<?php echo e($product->id); ?></td>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="#" class="avatar avatar-sm mr-2">
                                            <img class="avatar-img" src="<?php echo e($product->image ? asset('storage/'.$product->image) : asset('assets/img/specialities/specialities-01.png')); ?>" alt="Product">
                                        </a>
                                        <a href="#"><?php echo e($product->name); ?></a>
                                    </h2>
                                </td>
                                <td><?php echo e($product->category->name ?? 'N/A'); ?></td>
                                <td>$<?php echo e(number_format($product->price, 2)); ?></td>
                                <td><?php echo e($product->stock); ?></td>
                                <td>
                                    <div class="status-toggle">
                                        <input type="checkbox" id="status_<?php echo e($product->id); ?>" class="check" <?php echo e($product->is_active ? 'checked' : ''); ?> disabled>
                                        <label for="status_<?php echo e($product->id); ?>" class="checktoggle">checkbox</label>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="actions">
                                        <a class="btn btn-sm bg-success-light" href="<?php echo e(route('admin.products.edit', $product->id)); ?>">
                                            <i class="fe fe-pencil"></i> Edit
                                        </a>
                                        <form action="<?php echo e(route('admin.products.destroy', $product->id)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u190556289/domains/abcseba.com/public_html/Modules/Ecommerce/resources/views/backend/products/index.blade.php ENDPATH**/ ?>