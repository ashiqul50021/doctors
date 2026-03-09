<?php $__env->startSection('title', 'Health Courses - Doccure'); ?>

<?php $__env->startSection('content'); ?>

    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/courses.css')); ?>">
    <?php $__env->stopPush(); ?>

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-12 col-12">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Courses</li>
                        </ol>
                    </nav>
                    <h2 class="breadcrumb-title">Health Courses</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadcrumb -->

    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <!-- Search Filter (Optional, can be expanded) -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <form action="<?php echo e(route('courses.index')); ?>" method="GET">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Search courses..." value="<?php echo e(request('search')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <select class="form-control select" name="category">
                                                <option value="">All Categories</option>
                                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                                        <?php echo e($category->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary w-100" type="submit">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Courses Grid -->
                    <div class="row">
                        <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="course-card">
                                    <div class="course-thumbnail">
                                        <a href="<?php echo e(route('courses.show', $course->id)); ?>">
                                            <?php if(Str::startsWith($course->image, 'assets')): ?>
                                                <img src="<?php echo e(asset($course->image)); ?>" class="img-fluid" alt="<?php echo e($course->title); ?>">
                                            <?php else: ?>
                                                <img src="<?php echo e(asset('storage/' . $course->image)); ?>" class="img-fluid"
                                                    alt="<?php echo e($course->title); ?>">
                                            <?php endif; ?>
                                        </a>
                                        <div class="play-overlay">
                                            <a href="<?php echo e(route('courses.show', $course->id)); ?>">
                                                <i class="fas fa-play-circle"></i>
                                            </a>
                                        </div>
                                        <span class="course-badge <?php echo e($course->price == 0 ? 'free' : 'premium'); ?>">
                                            <?php echo e($course->price == 0 ? 'Free' : '৳' . number_format($course->price)); ?>

                                        </span>
                                    </div>
                                    <div class="course-content">
                                        <div class="course-meta">
                                            <span><i class="fas fa-clock"></i> <?php echo e($course->duration_hours); ?> hrs</span>
                                            <span><i class="fas fa-book"></i> <?php echo e($course->lessons->count()); ?> Lessons</span>
                                        </div>
                                        <h4 class="course-title">
                                            <a href="<?php echo e(route('courses.show', $course->id)); ?>"><?php echo e($course->title); ?></a>
                                        </h4>
                                        <p class="course-desc text-muted mb-3"
                                            style="font-size: 13px; line-height: 1.5; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?php echo e($course->short_description); ?>

                                        </p>
                                        <div class="course-footer">
                                            <div class="course-instructor">
                                                <img src="<?php echo e($course->instructor && $course->instructor->profile ? asset('storage/' . $course->instructor->profile) : asset('assets/img/doctors/doctor-thumb-01.jpg')); ?>"
                                                    alt="Instructor">
                                                <span>Dr. <?php echo e($course->instructor->name ?? 'Instructor'); ?></span>
                                            </div>
                                            <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="btn-enroll">
                                                <?php echo e($course->price == 0 ? 'Enroll' : 'Buy Now'); ?> <i
                                                    class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center p-5">
                                        <h3>No Courses Found</h3>
                                        <p class="text-muted">Try adjusting your search or filters.</p>
                                        <a href="<?php echo e(route('courses.index')); ?>" class="btn btn-primary mt-3">View All Courses</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="blog-pagination">
                                <?php echo e($courses->links()); ?>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/Modules/Courses/resources/views/frontend/courses/index.blade.php ENDPATH**/ ?>