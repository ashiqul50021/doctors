<?php $__env->startSection('title', 'Appointments - Doccure'); ?>

<?php $__env->startSection('content'); ?>

<!-- Page Content -->
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">

                <!-- Profile Sidebar -->
                <div class="profile-sidebar">
                    <div class="widget-profile pro-widget-content">
                        <div class="profile-info-widget">
                            <a href="#" class="booking-doc-img">
                                <img src="<?php echo e(asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>" alt="User Image">
                            </a>
                            <div class="profile-det-info">
                                <h3>Dr. Darren Elder</h3>

                                <div class="patient-details">
                                    <h5 class="mb-0">BDS, MDS - Oral & Maxillofacial Surgery</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-widget">
                        <nav class="dashboard-menu">
                            <ul>
                                <li>
                                    <a href="<?php echo e(route('doctor.dashboard')); ?>">
                                        <i class="fas fa-columns"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="<?php echo e(route('appointments')); ?>">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>Appointments</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('my.patients')); ?>">
                                        <i class="fas fa-user-injured"></i>
                                        <span>My Patients</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('schedule.timings')); ?>">
                                        <i class="fas fa-hourglass-start"></i>
                                        <span>Schedule Timings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('invoices')); ?>">
                                        <i class="fas fa-file-invoice"></i>
                                        <span>Invoices</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('reviews')); ?>">
                                        <i class="fas fa-star"></i>
                                        <span>Reviews</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('chat.doctor')); ?>">
                                        <i class="fas fa-comments"></i>
                                        <span>Message</span>
                                        <small class="unread-msg">23</small>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('doctor.profile.settings')); ?>">
                                        <i class="fas fa-user-cog"></i>
                                        <span>Profile Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('social.media')); ?>">
                                        <i class="fas fa-share-alt"></i>
                                        <span>Social Media</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('doctor.change.password')); ?>">
                                        <i class="fas fa-lock"></i>
                                        <span>Change Password</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('login')); ?>">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- /Profile Sidebar -->

            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="appointments">

                    <!-- Appointment List -->
                    <div class="appointment-list">
                        <div class="profile-info-widget">
                            <a href="<?php echo e(route('patient.profile')); ?>" class="booking-doc-img">
                                <img src="<?php echo e(asset('assets/img/patients/patient.jpg')); ?>" alt="User Image">
                            </a>
                            <div class="profile-det-info">
                                <h3><a href="<?php echo e(route('patient.profile')); ?>">Richard Wilson</a></h3>
                                <div class="patient-details">
                                    <h5><i class="far fa-clock"></i> 14 Nov 2019, 10.00 AM</h5>
                                    <h5><i class="fas fa-map-marker-alt"></i> Newyork, United States</h5>
                                    <h5><i class="fas fa-envelope"></i> richard@example.com</h5>
                                    <h5 class="mb-0"><i class="fas fa-phone"></i> +1 923 782 4575</h5>
                                </div>
                            </div>
                        </div>
                        <div class="appointment-action">
                            <a href="#" class="btn btn-sm bg-info-light" data-bs-toggle="modal" data-bs-target="#appt_details">
                                <i class="far fa-eye"></i> View
                            </a>
                            <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                <i class="fas fa-check"></i> Accept
                            </a>
                            <a href="javascript:void(0);" class="btn btn-sm bg-danger-light">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                    <!-- /Appointment List -->

                    <!-- Appointment List -->
                    <div class="appointment-list">
                        <div class="profile-info-widget">
                            <a href="<?php echo e(route('patient.profile')); ?>" class="booking-doc-img">
                                <img src="<?php echo e(asset('assets/img/patients/patient1.jpg')); ?>" alt="User Image">
                            </a>
                            <div class="profile-det-info">
                                <h3><a href="<?php echo e(route('patient.profile')); ?>">Charlene Reed </a></h3>
                                <div class="patient-details">
                                    <h5><i class="far fa-clock"></i> 12 Nov 2019, 5.00 PM</h5>
                                    <h5><i class="fas fa-map-marker-alt"></i> North Carolina, United States</h5>
                                    <h5><i class="fas fa-envelope"></i> charlenereed@example.com</h5>
                                    <h5 class="mb-0"><i class="fas fa-phone"></i> +1 828 632 9170</h5>
                                </div>
                            </div>
                        </div>
                        <div class="appointment-action">
                            <a href="#" class="btn btn-sm bg-info-light" data-bs-toggle="modal" data-bs-target="#appt_details">
                                <i class="far fa-eye"></i> View
                            </a>
                            <a href="javascript:void(0);" class="btn btn-sm bg-success-light">
                                <i class="fas fa-check"></i> Accept
                            </a>
                            <a href="javascript:void(0);" class="btn btn-sm bg-danger-light">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                    <!-- /Appointment List -->

                </div>
            </div>
        </div>

    </div>

</div>
<!-- /Page Content -->

<!-- Appointment Details Modal -->
<div class="modal fade custom-modal" id="appt_details">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="info-details">
                    <li>
                        <div class="details-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="title">#APT0001</span>
                                    <span class="text">21 Oct 2019 10:00 AM</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-end">
                                        <button type="button" class="btn bg-success-light btn-sm" id="topup_status">Completed</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li>
                        <span class="title">Status:</span>
                        <span class="text">Completed</span>
                    </li>
                    <li>
                        <span class="title">Confirm Date:</span>
                        <span class="text">29 Jun 2019</span>
                    </li>
                    <li>
                        <span class="title">Paid Amount</span>
                        <span class="text">$450</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- /Appointment Details Modal -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- Sticky Sidebar JS -->
<script src="<?php echo e(asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/frontend/appointments.blade.php ENDPATH**/ ?>