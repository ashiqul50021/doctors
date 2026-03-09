<?php $__env->startSection('title', 'Doctor Profile - Doccure'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Doctor Profile Custom CSS */
.doctor-profile-header {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
}

.doctor-profile-header .doc-img {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    border: 4px solid #f8fafc;
}

.doctor-profile-header .doc-name {
    font-size: 22px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.doctor-profile-header .doc-speciality {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 12px;
}

.doctor-profile-header .doc-department {
    font-size: 14px;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.doctor-profile-header .doc-department img {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

.doctor-stats {
    display: flex;
    gap: 20px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
}

.doctor-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #4b5563;
}

.doctor-stat-item i {
    color: #2563eb;
}

/* Section Styling */
.profile-section {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
}

.profile-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.profile-section-title i {
    color: #2563eb;
    margin-right: 10px;
}

/* Education & Experience Visuals */
.experience-box {
    position: relative;
    padding-left: 30px;
}

.experience-box::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 5px;
    bottom: -15px;
    width: 2px;
    background: #e5e7eb;
}

.experience-box:last-child::before {
    display: none;
}

.experience-list li {
    position: relative;
    margin-bottom: 20px;
}

.experience-list li:last-child {
    margin-bottom: 0;
}

.experience-list li::after {
    content: '';
    position: absolute;
    left: -30px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid #fff;
    background: #2563eb;
    box-shadow: 0 0 0 1px #2563eb;
}

.exp-year {
    color: #6b7280;
    font-size: 13px;
    margin-bottom: 4px;
    display: block;
}

.exp-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
    margin: 0;
}

.exp-place {
    color: #4b5563;
    font-size: 14px;
}

/* Sidebar Widgets */
.sidebar-widget {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    border: 1px solid #e5e7eb;
}

.sidebar-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 16px;
    border-left: 3px solid #2563eb;
    padding-left: 12px;
}

.booking-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
}

.booking-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    color: white;
}

/* Services Tags */
.service-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.service-tag {
    background: #eff6ff;
    color: #2563eb;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

/* Business Hours */
.hours-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.hours-list li {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
}

.hours-list li:last-child {
    border-bottom: none;
}

.hours-list .day {
    color: #4b5563;
    font-weight: 500;
}

.hours-list .time {
    color: #1f2937;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 16px;
}

.action-btn {
    flex: 1;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
    color: #4b5563;
    text-align: center;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}

.action-btn:hover {
    background: #f8fafc;
    color: #2563eb;
    border-color: #2563eb;
}

/* Layout Fixes */
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-md-2, .col-md-6, .col-md-4, .col-lg-8, .col-lg-4 {
    position: relative;
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
}

@media (min-width: 768px) {
    .col-md-2 { flex: 0 0 16.666667%; max-width: 16.666667%; }
    .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
    .col-md-6 { flex: 0 0 50%; max-width: 50%; }
}

@media (min-width: 992px) {
    .col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
    .col-lg-8 { flex: 0 0 66.666667%; max-width: 66.666667%; }
}
</style>

<div class="content" style="background: #f8fafc; min-height: 100vh;">
    <div class="container">
        
        <!-- Doctor Header (Hero-like) -->
        <div class="doctor-profile-header">
            <div class="row">
                <div class="col-md-2 text-center text-md-start">
                    <img src="<?php echo e($doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>" class="doc-img" alt="User Image">
                </div>
                <div class="col-md-6">
                    <h4 class="doc-name">Dr. <?php echo e($doctor->user->name); ?></h4>
                    <p class="doc-speciality"><?php echo e($doctor->qualifications); ?></p>
                    <p class="doc-department">
                        <?php if($doctor->speciality && $doctor->speciality->image): ?>
                        <img src="<?php echo e(asset($doctor->speciality->image)); ?>" alt="Speciality">
                        <?php endif; ?>
                        <?php echo e($doctor->speciality->name ?? 'General'); ?>

                    </p>
                    
                    <div class="rating mb-2">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo e($i <= $doctor->average_rating ? 'filled' : ''); ?>" style="color: <?php echo e($i <= $doctor->average_rating ? '#fbbf24' : '#e5e7eb'); ?>"></i>
                        <?php endfor; ?>
                        <span class="d-inline-block average-rating ms-2 text-muted">(<?php echo e($doctor->review_count); ?> Reviews)</span>
                    </div>

                    <div class="doctor-stats">
                        <div class="doctor-stat-item">
                            <i class="far fa-comment"></i> <?php echo e($doctor->review_count); ?> Feedback
                        </div>
                        <div class="doctor-stat-item">
                            <i class="fas fa-map-marker-alt"></i> <?php echo e($doctor->clinic_city); ?>

                        </div>
                        <div class="doctor-stat-item">
                            <i class="far fa-money-bill-alt"></i> <?php echo e($doctor->pricing === 'free' ? 'Free' : ($doctor->pricing === 'custom_price' ? '$'.$doctor->custom_price : 'N/A')); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex flex-column h-100 justify-content-center">
                        <div class="action-buttons mb-3">
                            <a href="javascript:void(0)" class="action-btn" title="Detailed Profile"><i class="far fa-user"></i> Profile</a>
                            <a href="javascript:void(0)" class="action-btn" title="Voice Call" data-bs-toggle="modal" data-bs-target="#voice_call"><i class="fas fa-phone-alt"></i> Call</a>
                            <a href="javascript:void(0)" class="action-btn" title="Video Call" data-bs-toggle="modal" data-bs-target="#video_call"><i class="fas fa-video"></i> Video</a>
                            <a href="<?php echo e(route('chat')); ?>" class="action-btn" title="Chat"><i class="far fa-comment-dots"></i> Chat</a>
                        </div>
                        <a href="<?php echo e(route('booking', $doctor->id)); ?>" class="booking-btn">
                            Book Appointment <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content (Left) -->
            <div class="col-lg-8">
                
                <!-- About Me -->
                <div class="profile-section">
                    <h3 class="profile-section-title"><i class="fas fa-user-md"></i> About Me</h3>
                    <div class="about-content">
                        <p class="text-muted mb-0"><?php echo e($doctor->bio ?? 'No biography available.'); ?></p>
                    </div>
                </div>

                <!-- Education & Experience (Placeholder/Static for now) -->
                <div class="profile-section">
                    <h3 class="profile-section-title"><i class="fas fa-graduation-cap"></i> Education & Experience</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-dark font-weight-bold">Education</h5>
                            <ul class="experience-list list-unstyled experience-box">
                                <li>
                                    <span class="exp-year">2008 - 2013</span>
                                    <h4 class="exp-title">MBBS</h4>
                                    <span class="exp-place">Dhaka Medical College</span>
                                </li>
                                <li>
                                    <span class="exp-year">2013 - 2015</span>
                                    <h4 class="exp-title">FCPS (Medicine)</h4>
                                    <span class="exp-place">Bangladesh College of Physicians and Surgeons</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3 text-dark font-weight-bold">Experience</h5>
                            <ul class="experience-list list-unstyled experience-box">
                                <li>
                                    <span class="exp-year">2016 - Present</span>
                                    <h4 class="exp-title">Senior Consultant</h4>
                                    <span class="exp-place">Square Hospital, Dhaka</span>
                                </li>
                                <li>
                                    <span class="exp-year">2014 - 2016</span>
                                    <h4 class="exp-title">Medical Officer</h4>
                                    <span class="exp-place">Dhaka Medical College Hospital</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Locations -->
                <div class="profile-section">
                    <h3 class="profile-section-title"><i class="fas fa-map-marked-alt"></i> Locations</h3>
                    <div class="location-list">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="clinic-content">
                                    <h4 class="clinic-name text-primary font-weight-bold mb-2"><?php echo e($doctor->clinic_name ?? 'Main Clinic'); ?></h4>
                                    <p class="doc-speciality mb-2"><?php echo e($doctor->qualifications); ?></p>
                                    <div class="clinic-details mb-0">
                                        <p class="mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i> <?php echo e($doctor->clinic_address); ?>, <?php echo e($doctor->clinic_city); ?></p>
                                        <p class="mb-0 text-primary" style="cursor: pointer;"><i class="fas fa-directions me-2"></i> Get Directions</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="clinic-timing">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Consultation Fee</span>
                                        <span class="font-weight-bold text-dark"><?php echo e($doctor->pricing === 'free' ? 'Free' : ($doctor->pricing === 'custom_price' ? '$'.$doctor->custom_price : 'N/A')); ?></span>
                                    </div>
                                    <div class="consult-price">
                                         
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="profile-section">
                    <h3 class="profile-section-title">
                        <span><i class="fas fa-star"></i> Reviews</span>
                        <a href="#" class="text-primary" style="font-size: 14px; font-weight: 500;">Write a Review</a>
                    </h3>
                    
                    <div class="reviews-list">
                        <?php $__empty_1 = true; $__currentLoopData = $doctor->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex gap-3">
                                <img class="avatar avatar-sm rounded-circle" alt="User Image" src="<?php echo e(optional($review->patient->user)->profile_image ? asset($review->patient->user->profile_image) : asset('assets/img/patients/patient.jpg')); ?>" style="width: 40px; height: 40px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 font-weight-bold"><?php echo e($review->patient->user->name ?? 'Patient'); ?></h6>
                                        <span class="text-muted small"><?php echo e($review->created_at->diffForHumans()); ?></span>
                                    </div>
                                    <div class="rating mb-2">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="font-size: 10px; color: <?php echo e($i <= $review->rating ? '#fbbf24' : '#e5e7eb'); ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-muted mb-0 small"><?php echo e($review->comment); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No reviews yet.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Sidebar (Right) -->
            <div class="col-lg-4">
                <div class="theiaStickySidebar">
                    
                    <!-- Business Hours -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Business Hours</h4>
                        <ul class="hours-list">
                            <?php $__empty_1 = true; $__currentLoopData = $doctor->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li>
                                <span class="day"><?php echo e(ucfirst($schedule->day)); ?></span>
                                <span class="time"><?php echo e(\Carbon\Carbon::parse($schedule->start_time)->format('g:i A')); ?> - <?php echo e(\Carbon\Carbon::parse($schedule->end_time)->format('g:i A')); ?></span>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li><span class="text-muted">No schedules available</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Services</h4>
                        <div class="service-tags">
                            <?php if(isset($doctor->services) && !empty($doctor->services)): ?>
                                <?php $__currentLoopData = json_decode($doctor->services) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="service-tag"><?php echo e($service); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <span class="service-tag">General Consultation</span>
                                <span class="service-tag">Online Support</span>
                                <span class="service-tag">Medical Checkup</span>
                                <span class="service-tag">Health Advice</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Share Profile -->
                    <div class="sidebar-widget">
                        <h4 class="sidebar-title">Share Profile</h4>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary btn-sm flex-fill"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-info btn-sm flex-fill"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-secondary btn-sm flex-fill"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="btn btn-outline-success btn-sm flex-fill"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js')); ?>"></script>
<script>
    $(document).ready(function() {
        if ($(window).width() > 991) {
            $('.theiaStickySidebar').theiaStickySidebar({
                additionalMarginTop: 100
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/frontend/doctor-profile.blade.php ENDPATH**/ ?>