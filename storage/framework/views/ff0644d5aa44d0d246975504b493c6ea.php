<?php $__env->startSection('title', 'Admin Dashboard - ' . ($siteSettings['site_name'] ?? 'Doccure')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Welcome Admin!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary border-primary">
                            <i class="fas fa-user-md"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?php echo e($doctorCount); ?></h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Doctors</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-success">
                            <i class="fas fa-user-injured"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?php echo e($patientCount); ?></h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Patients</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success" style="width: 60%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-danger border-danger">
                            <i class="fas fa-calendar-check"></i>
                        </span>
                        <div class="dash-count">
                            <h3><?php echo e($appointmentCount); ?></h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Appointments</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-danger" style="width: 70%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-warning border-warning">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <div class="dash-count">
                            <h3>$62523</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Revenue</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning" style="width: 80%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-md-7">
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title">Revenue Overview</h4>
                </div>
                <div class="card-body">
                    <div id="revenue_chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title">Users Distribution</h4>
                </div>
                <div class="card-body">
                    <div id="users_chart"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Charts Section -->

    <!-- Recent Appointments -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-table">
                <div class="card-header">
                    <h4 class="card-title">Recent Appointments</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Speciality</th>
                                    <th>Patient Name</th>
                                    <th>Apointment Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="table-avatar">
                                        <a href="<?php echo e(route('admin.doctors.index')); ?>" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-circle"
                                                src="<?php echo e(asset('assets/img/doctors/doctor-thumb-01.jpg')); ?>"
                                                alt="User Image">
                                        </a>
                                        <a href="<?php echo e(route('admin.doctors.index')); ?>">Dr. Ruby Perrin</a>
                                    </td>
                                    <td>Dental</td>
                                    <td>
                                        <a href="<?php echo e(route('admin.patients')); ?>" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-circle"
                                                src="<?php echo e(asset('assets/img/patients/patient1.jpg')); ?>" alt="User Image">
                                        </a>
                                        <a href="<?php echo e(route('admin.patients')); ?>">Charlene Reed</a>
                                    </td>
                                    <td>9 Nov 2019 <span class="text-primary d-block">11.00 AM - 11.15 AM</span></td>
                                    <td>
                                        <div class="status-toggle">
                                            <input type="checkbox" id="status_1" class="check" checked>
                                            <label for="status_1" class="checktoggle">checkbox</label>
                                        </div>
                                    </td>
                                    <td class="text-end">$200.00</td>
                                </tr>
                                <tr>
                                    <td class="table-avatar">
                                        <a href="<?php echo e(route('admin.doctors.index')); ?>" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-circle"
                                                src="<?php echo e(asset('assets/img/doctors/doctor-thumb-02.jpg')); ?>"
                                                alt="User Image">
                                        </a>
                                        <a href="<?php echo e(route('admin.doctors.index')); ?>">Dr. Darren Elder</a>
                                    </td>
                                    <td>Dental</td>
                                    <td>
                                        <a href="<?php echo e(route('admin.patients')); ?>" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-circle"
                                                src="<?php echo e(asset('assets/img/patients/patient2.jpg')); ?>" alt="User Image">
                                        </a>
                                        <a href="<?php echo e(route('admin.patients')); ?>">Travis Trimble</a>
                                    </td>
                                    <td>5 Nov 2019 <span class="text-primary d-block">11.00 AM - 11.35 AM</span></td>
                                    <td>
                                        <div class="status-toggle">
                                            <input type="checkbox" id="status_2" class="check" checked>
                                            <label for="status_2" class="checktoggle">checkbox</label>
                                        </div>
                                    </td>
                                    <td class="text-end">$300.00</td>
                                </tr>
                                <tr>
                                    <td class="table-avatar">
                                        <a href="<?php echo e(route('admin.doctors.index')); ?>" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-circle"
                                                src="<?php echo e(asset('assets/img/doctors/doctor-thumb-03.jpg')); ?>"
                                                alt="User Image">
                                        </a>
                                        <a href="<?php echo e(route('admin.doctors.index')); ?>">Dr. Deborah Angel</a>
                                    </td>
                                    <td>Cardiology</td>
                                    <td>
                                        <a href="<?php echo e(route('admin.patients')); ?>" class="avatar avatar-sm me-2">
                                            <img class="avatar-img rounded-circle"
                                                src="<?php echo e(asset('assets/img/patients/patient3.jpg')); ?>" alt="User Image">
                                        </a>
                                        <a href="<?php echo e(route('admin.patients')); ?>">Carl Kelly</a>
                                    </td>
                                    <td>11 Nov 2019 <span class="text-primary d-block">12.00 PM - 12.15 PM</span></td>
                                    <td>
                                        <div class="status-toggle">
                                            <input type="checkbox" id="status_3" class="check" checked>
                                            <label for="status_3" class="checktoggle">checkbox</label>
                                        </div>
                                    </td>
                                    <td class="text-end">$150.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Recent Appointments -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        $(document).ready(function () {

            // Revenue Chart (Area Chart)
            var optionsRevenue = {
                series: [{
                    name: 'Revenue',
                    data: [3100, 4000, 2800, 5100, 4200, 6900, 10000]
                }, {
                    name: 'Expenses',
                    data: [1100, 3200, 4500, 3200, 3400, 5200, 4100]
                }],
                chart: {
                    height: 350,
                    type: 'area',
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'datetime',
                    categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    },
                },
                colors: ['#1E40AF', '#00D0F1'], // Premium Blue & Cyan
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.9,
                        stops: [0, 90, 100]
                    }
                }
            };

            var chartRevenue = new ApexCharts(document.querySelector("#revenue_chart"), optionsRevenue);
            chartRevenue.render();

            // Users Chart (Donut)
            var optionsUsers = {
                series: [<?php echo e($doctorCount); ?>, <?php echo e($patientCount); ?>, 25], // Added mock 'Others' for visual balance
                chart: {
                    type: 'donut',
                    height: 350,
                },
                labels: ['Doctors', 'Patients', 'Staff'],
                colors: ['#1E40AF', '#10B981', '#F59E0B'], // Blue, Green, Amber
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chartUsers = new ApexCharts(document.querySelector("#users_chart"), optionsUsers);
            chartUsers.render();
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ashiquli/doctors.ashiqulislamrasel.com/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>