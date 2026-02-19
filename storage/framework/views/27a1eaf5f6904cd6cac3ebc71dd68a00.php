<?php $__env->startSection('title', 'Add New Polling Unit Results'); ?>
<?php $__env->startSection('page-title', 'Add New Polling Unit Results'); ?>
<?php $__env->startSection('page-subtitle', 'Store election results for all parties at a new polling unit'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card">
            <div class="card-header">
                <i class="bi bi-plus-circle-fill me-2"></i>Enter Results
            </div>
            <div class="card-body p-4">

                <form action="<?php echo e(route('new-result.store')); ?>" method="POST" id="resultForm">
                    <?php echo csrf_field(); ?>

                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    
                    <h6 class="text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1px;">
                        Step 1 &mdash; Select Location
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">State</label>
                            <select id="stateSelect" class="form-select">
                                <option value="">-- Select State --</option>
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($state->state_id); ?>"
                                        <?php echo e($state->state_id == $defaultStateId ? 'selected' : ''); ?>>
                                        <?php echo e($state->state_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">LGA</label>
                            <select id="lgaSelect" class="form-select" disabled>
                                <option value="">-- Select LGA --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ward</label>
                            <select id="wardSelect" class="form-select" disabled>
                                <option value="">-- Select Ward --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Polling Unit</label>
                            <select id="pollingUnitSelect" name="polling_unit_id" class="form-select" disabled>
                                <option value="">-- Select Polling Unit --</option>
                            </select>
                            <?php $__errorArgs = ['polling_unit_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    
                    <h6 class="text-muted text-uppercase mb-3" style="font-size:0.75rem; letter-spacing:1px;">
                        Step 2 &mdash; Enter Party Scores
                    </h6>

                    <div id="scoresPlaceholder" class="text-center py-4 text-muted">
                        <i class="bi bi-arrow-up-circle" style="font-size:2rem; opacity:0.3;"></i>
                        <p class="mt-2 small">Select a polling unit above to enter scores.</p>
                    </div>

                    <div id="scoresSection" style="display:none;">
                        <div class="row g-3" id="partyScores">
                            <?php $__currentLoopData = $parties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $party): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">
                                        <span class="badge-party"><?php echo e($party->partyid); ?></span>
                                    </label>
                                    <input
                                        type="number"
                                        name="scores[<?php echo e($party->partyid); ?>]"
                                        class="form-control"
                                        placeholder="Enter votes"
                                        min="0"
                                        value="<?php echo e(old('scores.' . $party->partyid, 0)); ?>"
                                        required
                                    >
                                    <?php $__errorArgs = ['scores.' . $party->partyid];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                Any existing results for this polling unit will be replaced.
                            </div>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-save-fill me-2"></i>Save Results
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const stateSelect        = document.getElementById('stateSelect');
    const lgaSelect          = document.getElementById('lgaSelect');
    const wardSelect         = document.getElementById('wardSelect');
    const pollingUnitSelect  = document.getElementById('pollingUnitSelect');
    const scoresPlaceholder  = document.getElementById('scoresPlaceholder');
    const scoresSection      = document.getElementById('scoresSection');

    function resetSelect(el, placeholder) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
        el.disabled = true;
    }

    // State → LGAs
    stateSelect.addEventListener('change', function () {
        resetSelect(lgaSelect, '-- Select LGA --');
        resetSelect(wardSelect, '-- Select Ward --');
        resetSelect(pollingUnitSelect, '-- Select Polling Unit --');
        scoresSection.style.display = 'none';
        scoresPlaceholder.style.display = 'block';

        if (!this.value) return;

        fetch(`/ajax/lgas/${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(lga => {
                    lgaSelect.innerHTML += `<option value="${lga.lga_id}">${lga.lga_name}</option>`;
                });
                lgaSelect.disabled = false;
            });
    });

    // LGA → Wards
    lgaSelect.addEventListener('change', function () {
        resetSelect(wardSelect, '-- Select Ward --');
        resetSelect(pollingUnitSelect, '-- Select Polling Unit --');
        scoresSection.style.display = 'none';
        scoresPlaceholder.style.display = 'block';

        if (!this.value) return;

        fetch(`/ajax/wards/${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(ward => {
                    wardSelect.innerHTML += `<option value="${ward.ward_id}">${ward.ward_name}</option>`;
                });
                wardSelect.disabled = false;
            });
    });

    // Ward → Polling Units
    wardSelect.addEventListener('change', function () {
        resetSelect(pollingUnitSelect, '-- Select Polling Unit --');
        scoresSection.style.display = 'none';
        scoresPlaceholder.style.display = 'block';

        if (!this.value) return;

        fetch(`/ajax/polling-units/${this.value}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(pu => {
                    const label = pu.polling_unit_number
                        ? `${pu.polling_unit_number} - ${pu.polling_unit_name}`
                        : pu.polling_unit_name;
                    pollingUnitSelect.innerHTML += `<option value="${pu.uniqueid}">${label}</option>`;
                });
                pollingUnitSelect.disabled = false;
            });
    });

    // Polling Unit → show score inputs
    pollingUnitSelect.addEventListener('change', function () {
        if (this.value) {
            scoresPlaceholder.style.display = 'none';
            scoresSection.style.display = 'block';
        } else {
            scoresPlaceholder.style.display = 'block';
            scoresSection.style.display = 'none';
        }
    });

    // Auto-trigger state
    if (stateSelect.value) {
        stateSelect.dispatchEvent(new Event('change'));
    }

    // Prevent submit without polling unit
    document.getElementById('resultForm').addEventListener('submit', function (e) {
        if (!pollingUnitSelect.value) {
            e.preventDefault();
            alert('Please select a polling unit before saving.');
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/new-result/index.blade.php ENDPATH**/ ?>