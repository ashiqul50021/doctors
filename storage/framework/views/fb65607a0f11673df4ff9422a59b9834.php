<div class="sort-bar">
    <div class="results-count">
        <strong><?php echo e($doctors->total()); ?></strong> doctors found
    </div>
    <div class="sort-options">
        <label>Sort by:</label>
        <select class="sort-select" onchange="changeSortBy(this.value)">
            <option value="relevance" <?php echo e(request('sort_by') == 'relevance' ? 'selected' : ''); ?>>Relevance</option>
            <option value="fee_low" <?php echo e(request('sort_by') == 'fee_low' ? 'selected' : ''); ?>>Fee: Low to High</option>
            <option value="fee_high" <?php echo e(request('sort_by') == 'fee_high' ? 'selected' : ''); ?>>Fee: High to Low</option>
            <option value="experience" <?php echo e(request('sort_by') == 'experience' ? 'selected' : ''); ?>>Experience</option>
            <option value="rating" <?php echo e(request('sort_by') == 'rating' ? 'selected' : ''); ?>>Rating</option>
            <option value="newest" <?php echo e(request('sort_by') == 'newest' ? 'selected' : ''); ?>>Newest</option>
        </select>
    </div>
</div>

<?php $__empty_1 = true; $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php echo $__env->make('components.search-doctor-card', [
        'image' => $doctor->profile_image ? (filter_var($doctor->profile_image, FILTER_VALIDATE_URL) ? $doctor->profile_image : asset($doctor->profile_image)) : asset('assets/img/doctors/doctor-thumb-01.jpg'),
        'name' => $doctor->user->name,
        'speciality' => $doctor->speciality->name ?? 'General',
        'department' => $doctor->speciality->name ?? 'General',
        'departmentIcon' => ($doctor->speciality && $doctor->speciality->image && file_exists(public_path($doctor->speciality->image))) ? asset($doctor->speciality->image) : asset('assets/img/specialities/specialities-05.png'),
        'rating' => $doctor->average_rating,
        'reviews' => $doctor->review_count,
        'location' => ($doctor->area ? $doctor->area->name . ', ' : '') . ($doctor->district ? $doctor->district->name : 'Location'),
        'price' => $doctor->consultation_fee > 0 ? $doctor->consultation_fee : 'Free',
        'thumbsUp' => $doctor->average_rating > 0 ? round(($doctor->average_rating / 5) * 100) . '%' : '0%',
        'experience' => $doctor->experience_years ?? 0,
        'qualification' => $doctor->qualification ?? '',
        'profileLink' => route('doctor.profile', $doctor->id),
        'bookingLink' => route('booking', $doctor->id)
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


               <?php if(($loop->index + 1) % 3 == 0 && isset($advertisements) && $advertisements->count() > 0): ?>
                <?php
                    // Calculate sequential index: (3rd item -> 0, 6th item -> 1, 9th item -> 2)
                    $adIndex = (int) (($loop->iteration / 3) - 1);
                    // Get ad using modulo to rotate if we have fewer ads than slots
                    $ad = $advertisements->values()->get($adIndex % $advertisements->count());
                ?>
                        <?php if($ad): ?>
                            <div class="card mb-3 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e4e4e4;">
                                <div class="card-body p-0 position-relative">
                                    <a href="<?php echo e($ad->link ?? '#'); ?>" target="_blank" class="d-block text-center" style="background: #f8f9fa;">
                                        <img src="<?php echo e(asset($ad->image)); ?>" class="img-fluid" alt="<?php echo e($ad->title); ?>" style="width: 100%; max-height: 250px; object-fit: cover;">
                                        </a>
                                        <span class="badge badge-light text-muted position-absolute" style="top: 10px; right: 10px; background: rgba(255,255,255,0.8);">Sponsored</span>
                                    </div>
                            </div>
                        <?php endif; ?>
            <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>    <div class="text-center py-5">
        <img src="<?php echo e(asset('assets/img/no-results.svg')); ?>" alt="No Results" style="max-width: 200px; opacity: 0.5;" onerror="this.style.display='none'">
        <h4 class="mt-4" style="color: #6b7280;">No doctors found</h4>
        <p style="color: #9ca3af;">Try adjusting your search filters</p>
        <button onclick="resetFilters()" class="btn btn-primary mt-3">
            <i class="fas fa-redo"></i> Reset Filters
        </button>
    </div>
<?php endif; ?>

<?php if($doctors->count() > 0): ?>
    <div class="load-more text-center mt-4">
        <?php echo e($doctors->withQueryString()->links()); ?>

    </div>
<?php endif; ?>
<?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/frontend/super-doctor-list.blade.php ENDPATH**/ ?>