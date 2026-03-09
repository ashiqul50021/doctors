<?php $__env->startSection('title', 'Booking - Doccure'); ?>

<?php $__env->startSection('content'); ?>

    <!-- Page Content -->
    <div class="content">
        <div class="container">

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="booking-doc-info">
                                <a href="<?php echo e(route('doctor.profile', $doctor->id)); ?>" class="booking-doc-img">
                                    <img src="<?php echo e($doctor->profile_image ? asset($doctor->profile_image) : asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>"
                                        alt="User Image">
                                </a>
                                <div class="booking-info">
                                    <h4><a href="<?php echo e(route('doctor.profile', $doctor->id)); ?>">Dr.
                                            <?php echo e($doctor->user->name); ?></a></h4>
                                    <div class="rating">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo e($i <= $doctor->average_rating ? 'filled' : ''); ?>"></i>
                                        <?php endfor; ?>
                                        <span class="d-inline-block average-rating"><?php echo e($doctor->review_count); ?></span>
                                    </div>
                                    <p class="text-muted mb-0"><i class="fas fa-map-marker-alt"></i>
                                        <?php echo e($doctor->clinic_city); ?>, <?php echo e($doctor->clinic_address); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Widget -->
                    <form action="<?php echo e(route('booking.submit', $doctor->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="appointment_date" id="appointment_date">
                        <input type="hidden" name="appointment_time" id="appointment_time">

                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title">Appointment Type</h4>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_offline"
                                        value="offline" checked>
                                    <label class="form-check-label" for="type_offline">Offline (Visit Clinic)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="type_online"
                                        value="online">
                                    <label class="form-check-label" for="type_online">Online (Video Call)</label>
                                </div>
                            </div>
                        </div>

                        <div class="card booking-schedule schedule-widget">

                            <!-- Schedule Header -->
                            <div class="schedule-header">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Day Slot -->
                                        <div class="day-slot">
                                            <ul>
                                                <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <span><?php echo e($date->format('D')); ?></span>
                                                        <span class="slot-date"><?php echo e($date->format('d M')); ?> <small
                                                                class="slot-year"><?php echo e($date->format('Y')); ?></small></span>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                        <!-- /Day Slot -->
                                    </div>
                                </div>
                            </div>
                            <!-- /Schedule Header -->

                            <!-- Schedule Content -->
                            <div class="schedule-cont">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Time Slot -->
                                        <div class="time-slot">
                                            <ul class="clearfix">
                                                <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <?php
                                                            $dayName = strtolower($date->format('l'));
                                                            $daySchedule = $doctor->schedules->where('day', $dayName)->first();
                                                        ?>

                                                        <?php if($daySchedule): ?>
                                                            <?php
                                                                $startTime = \Carbon\Carbon::parse($daySchedule->start_time);
                                                                $endTime = \Carbon\Carbon::parse($daySchedule->end_time);
                                                                $interval = $daySchedule->slot_duration ?? 60; // Default 60 mins
                                                            ?>

                                                            <?php while($startTime < $endTime): ?>
                                                                <a class="timing" href="javascript:void(0)"
                                                                    onclick="selectSlot(this, '<?php echo e($date->format('Y-m-d')); ?>', '<?php echo e($startTime->format('H:i')); ?>')">
                                                                    <span><?php echo e($startTime->format('g:i A')); ?></span>
                                                                </a>
                                                                <?php $startTime->addMinutes($interval); ?>
                                                            <?php endwhile; ?>
                                                        <?php else: ?>
                                                            <a class="timing disabled" href="javascript:void(0)">
                                                                <span>Closed</span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                        <!-- /Time Slot -->
                                    </div>
                                </div>
                            </div>
                            <!-- /Schedule Content -->

                        </div>
                        <!-- /Schedule Widget -->

                        <!-- Submit Section -->
                        <div class="submit-section proceed-btn text-end">
                            <button type="submit" class="btn btn-primary submit-btn">Proceed to Pay</button>
                        </div>
                        <!-- /Submit Section -->
                    </form>

                    <script>
                        function selectSlot(element, date, time) {
                            // Remove selected class from all slots
                            document.querySelectorAll('.timing').forEach(el => el.classList.remove('selected'));
                            // Add selected class to clicked slot
                            element.classList.add('selected');
                            // Set hidden inputs
                            document.getElementById('appointment_date').value = date;
                            document.getElementById('appointment_time').value = time;
                        }
                    </script>

                </div>
            </div>
        </div>

    </div>
    <!-- /Page Content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/frontend/booking.blade.php ENDPATH**/ ?>