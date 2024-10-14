<?php $__env->startSection('content'); ?>
    <div class="container-xxl flex-grow-1 container-p-y">
        <a href="<?php echo e(url('company-create')); ?>" class="col-2 m-3 ms-auto">
            <button type="button" class="btn btn-primary mb-4">
                <i class='bx bxs-add-to-queue'></i>&nbsp; เพิ่มบริษัท
            </button>
        </a>

        <?php if(session('success') || session('message')): ?>
            <div class="alert alert-info mb-3">
                <?php echo e(session('success')); ?> <?php echo e(session('message')); ?>

            </div>
        <?php endif; ?>

        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="ค้นหาบริษัท..." />
        </div>

        <div class="row">
            <?php
                $colors = [
                    '#007bff', // --bs-blue
                    '#6610f2', // --bs-indigo
                    '#696cff', // --bs-purple
                    '#e83e8c', // --bs-pink
                    '#ff3e1d', // --bs-red
                    '#fd7e14', // --bs-orange
                    '#ffab00', // --bs-yellow
                    '#71dd37', // --bs-green
                    '#20c997', // --bs-teal
                    '#03c3ec', // --bs-cyan
                    '#233446', // --bs-dark
                ];
            ?>
            <?php $__currentLoopData = $query; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $que): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <i class='bx bxs-buildings'
                                    style="font-size: 32px; color: <?php echo e($colors[$index % count($colors)]); ?>;"></i>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="<?php echo e(url('company-edit', $que->id)); ?>"><i
                                                class="bx bx-edit-alt me-2"></i> Edit</a>
                                        <a class="dropdown-item" href="<?php echo e(url('company-delete', $que->id)); ?>"
                                            onclick="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?');"><i
                                                class="bx bx-trash me-2"></i> Delete</a>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo e(url('select-card', $que->id)); ?>">
                                <div class="card-text">
                                    <span class="fw-semibold d-block mb-1"><?php echo e($que->company); ?></span>
                                    <small class="d-block">General Ledger <span
                                            style="float: right;"><?php echo e($que->general_ledger_count); ?></span></small>
                                    <small class="d-block">General Ledger Sub <span
                                            style="float: right;"><?php echo e($que->general_ledger_sub_count); ?></span></small>
                                    <small class="d-block">Account_Code <span
                                            style="float: right;"><?php echo e($que->account_code_count); ?></span></small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const cards = document.querySelectorAll('.col-3');

            searchInput.addEventListener('input', function() {
                const searchValue = searchInput.value.toLowerCase();

                cards.forEach(card => {
                    const companyName = card.querySelector('.card-text .fw-semibold').textContent
                        .toLowerCase();

                    if (companyName.includes(searchValue)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.appHome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/project/google-sheet/resources/views/home.blade.php ENDPATH**/ ?>