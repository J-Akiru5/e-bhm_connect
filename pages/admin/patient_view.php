<?php
// pages/admin/patient_view.php
// View a single patient's full information (read-only)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id']) || trim($_GET['id']) === '') {
    $_SESSION['form_error'] = 'No patient ID provided.';
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}

$patient_id = (int) $_GET['id'];

try {
    // Patient info
    $stmt = $pdo->prepare('SELECT * FROM patients WHERE patient_id = :id LIMIT 1');
    $stmt->execute([':id' => $patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        $_SESSION['form_error'] = 'Patient not found.';
        header('Location: ' . BASE_URL . 'admin-patients');
        exit();
    }

    // Health records
    $stmt2 = $pdo->prepare('SELECT * FROM patient_health_records WHERE patient_id = :id LIMIT 1');
    $stmt2->execute([':id' => $patient_id]);
    $health_records = $stmt2->fetch(PDO::FETCH_ASSOC);

    // Vitals history
    $stmt3 = $pdo->prepare('SELECT * FROM patient_vitals WHERE patient_id = :id ORDER BY recorded_at DESC');
    $stmt3->execute([':id' => $patient_id]);
    $vitals = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Visits history
    $stmt4 = $pdo->prepare('SELECT * FROM health_visits WHERE patient_id = :id ORDER BY visit_date DESC');
    $stmt4->execute([':id' => $patient_id]);
    $visits = $stmt4->fetchAll(PDO::FETCH_ASSOC);

    // Family composition
    $stmt5 = $pdo->prepare('SELECT * FROM family_composition WHERE head_patient_id = :id');
    $stmt5->execute([':id' => $patient_id]);
    $family = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    // Available medication inventory (show all, will disable out-of-stock in dropdown)
    $stmtInv = $pdo->prepare('SELECT * FROM medication_inventory ORDER BY item_name ASC');
    $stmtInv->execute();
    $inventory = $stmtInv->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('Patient view load error: ' . $e->getMessage());
    $_SESSION['form_error'] = 'An error occurred while loading patient data.';
    header('Location: ' . BASE_URL . 'admin-patients');
    exit();
}
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?php echo htmlspecialchars($patient['full_name'] ?? 'Patient'); ?></h1>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>admin-patient-form?id=<?php echo $patient_id; ?>" class="btn btn-primary btn-sm">Edit Patient</a>
            <a href="<?php echo BASE_URL; ?>?action=report-patient-record&id=<?php echo $patient_id; ?>" class="btn btn-info btn-sm" target="_blank">Download PDF</a>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#dispenseModal">Dispense Medicine</button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Personal Information</div>
                <div class="card-body">
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($patient['address'] ?? ''); ?></p>
                    <p><strong>Birthdate:</strong> <?php echo htmlspecialchars($patient['birthdate'] ?? ''); ?></p>
                    <p><strong>Sex:</strong> <?php echo htmlspecialchars($patient['sex'] ?? ''); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($patient['contact'] ?? ''); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Health Records</div>
                <div class="card-body">
                    <?php if ($health_records): ?>
                        <dl class="row">
                            <dt class="col-sm-4">Medical History</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['medical_history'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Immunization Records</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['immunization_records'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Medication Records</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['medication_records'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Maternal & Child Health</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['maternal_child_health'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Chronic Disease Mgmt</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['chronic_disease_mgmt'] ?? '')); ?></dd>

                            <dt class="col-sm-4">Referral Information</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($health_records['referral_information'] ?? '')); ?></dd>
                        </dl>
                    <?php else: ?>
                        <div class="alert alert-info">No health records available for this patient.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Family Composition</div>
                <div class="card-body">
                    <?php if (empty($family)): ?>
                        <div class="alert alert-info">No family composition records found.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Relationship</th>
                                        <th>Health Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($family as $member): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($member['member_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($member['relationship'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($member['health_status'] ?? ''); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Vitals History</div>
                <div class="card-body">
                    <!-- Add Vital Sign Form -->
                    <form method="post" action="<?php echo BASE_URL; ?>?action=save-vital" class="mb-3">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Blood Pressure</label>
                                <input type="text" name="blood_pressure" class="form-control" placeholder="e.g., 120/80">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Heart Rate</label>
                                <input type="number" name="heart_rate" class="form-control" min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Temperature</label>
                                <input type="number" name="temperature" class="form-control" step="0.1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Notes</label>
                                <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success btn-sm">Save Vital</button>
                        </div>
                    </form>

                    <?php if (empty($vitals)): ?>
                        <div class="alert alert-info">No vitals recorded.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Recorded At</th>
                                        <th>Blood Pressure</th>
                                        <th>Heart Rate</th>
                                        <th>Temperature</th>
                                        <th>Notes</th>
                                        <?php if (is_superadmin() || has_permission('manage_patients')): ?>
                                            <th width="100">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vitals as $v): ?>
                                        <tr id="vital-row-<?php echo $v['vital_id']; ?>" data-vital-id="<?php echo $v['vital_id']; ?>">
                                            <td><?php echo htmlspecialchars($v['recorded_at'] ?? ''); ?></td>
                                            <td class="vital-bp-cell">
                                                <span class="view-mode"><?php echo htmlspecialchars($v['blood_pressure'] ?? ''); ?></span>
                                                <input type="text" class="form-control form-control-sm edit-mode" value="<?php echo htmlspecialchars($v['blood_pressure'] ?? ''); ?>" placeholder="120/80" style="display:none;">
                                            </td>
                                            <td class="vital-hr-cell">
                                                <span class="view-mode"><?php echo htmlspecialchars($v['heart_rate'] ?? ''); ?></span>
                                                <input type="number" class="form-control form-control-sm edit-mode" value="<?php echo htmlspecialchars($v['heart_rate'] ?? ''); ?>" min="0" style="display:none;">
                                            </td>
                                            <td class="vital-temp-cell">
                                                <span class="view-mode"><?php echo htmlspecialchars($v['temperature'] ?? ''); ?></span>
                                                <input type="number" class="form-control form-control-sm edit-mode" value="<?php echo htmlspecialchars($v['temperature'] ?? ''); ?>" step="0.1" style="display:none;">
                                            </td>
                                            <td class="vital-notes-cell">
                                                <span class="view-mode"><?php echo htmlspecialchars($v['notes'] ?? ''); ?></span>
                                                <input type="text" class="form-control form-control-sm edit-mode" value="<?php echo htmlspecialchars($v['notes'] ?? ''); ?>" style="display:none;">
                                            </td>
                                            <?php if (is_superadmin() || has_permission('manage_patients')): ?>
                                                <td class="actions-cell">
                                                    <button class="btn btn-sm btn-primary edit-vital-btn" onclick="editVital(<?php echo $v['vital_id']; ?>)">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-success save-vital-btn" onclick="saveVital(<?php echo $v['vital_id']; ?>)" style="display:none;">
                                                        <i class="fas fa-check"></i> Save
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary cancel-vital-btn" onclick="cancelEditVital(<?php echo $v['vital_id']; ?>)" style="display:none;">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Health Visit History</div>
                <div class="card-body">
                    <!-- Add Health Visit Form -->
                    <form method="post" action="<?php echo BASE_URL; ?>?action=save-visit" class="mb-3">
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Visit Date</label>
                                <input type="date" name="visit_date" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Visit Type <span class="text-danger">*</span></label>
                                <select name="visit_type" class="form-control" required>
                                    <option value="">-- Select Visit Type --</option>
                                    <option value="Home Visit">Home Visit</option>
                                    <option value="Healthcare Visit">Healthcare Visit</option>
                                    <option value="Follow-up Visit">Follow-up Visit</option>
                                    <option value="Emergency Visit">Emergency Visit</option>
                                    <option value="Prenatal Care">Prenatal Care</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-success btn-sm">Save Visit</button>
                        </div>
                    </form>

                    <?php if (empty($visits)): ?>
                        <div class="alert alert-info">No visits recorded.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Visit Date</th>
                                        <th>Type</th>
                                        <th>Remarks</th>
                                        <?php if (is_superadmin() || has_permission('manage_patients')): ?>
                                            <th width="100">Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($visits as $visit): ?>
                                        <tr id="visit-row-<?php echo $visit['visit_id']; ?>" data-visit-id="<?php echo $visit['visit_id']; ?>">
                                            <td class="visit-date-cell">
                                                <span class="view-mode"><?php echo htmlspecialchars($visit['visit_date'] ?? ''); ?></span>
                                                <input type="date" class="form-control form-control-sm edit-mode" value="<?php echo htmlspecialchars($visit['visit_date'] ?? ''); ?>" style="display:none;">
                                            </td>
                                            <td class="visit-type-cell">
                                                <span class="view-mode"><?php echo htmlspecialchars($visit['visit_type'] ?? ''); ?></span>
                                                <select class="form-control form-control-sm edit-mode" style="display:none;">
                                                    <option value="Home Visit" <?php echo ($visit['visit_type'] === 'Home Visit') ? 'selected' : ''; ?>>Home Visit</option>
                                                    <option value="Healthcare Visit" <?php echo ($visit['visit_type'] === 'Healthcare Visit') ? 'selected' : ''; ?>>Healthcare Visit</option>
                                                    <option value="Follow-up Visit" <?php echo ($visit['visit_type'] === 'Follow-up Visit') ? 'selected' : ''; ?>>Follow-up Visit</option>
                                                    <option value="Emergency Visit" <?php echo ($visit['visit_type'] === 'Emergency Visit') ? 'selected' : ''; ?>>Emergency Visit</option>
                                                    <option value="Prenatal Care" <?php echo ($visit['visit_type'] === 'Prenatal Care') ? 'selected' : ''; ?>>Prenatal Care</option>
                                                </select>
                                            </td>
                                            <td class="visit-remarks-cell">
                                                <span class="view-mode"><?php echo nl2br(htmlspecialchars($visit['remarks'] ?? '')); ?></span>
                                                <input type="text" class="form-control form-control-sm edit-mode" value="<?php echo htmlspecialchars($visit['remarks'] ?? ''); ?>" style="display:none;">
                                            </td>
                                            <?php if (is_superadmin() || has_permission('manage_patients')): ?>
                                                <td class="actions-cell">
                                                    <button class="btn btn-sm btn-primary edit-visit-btn" onclick="editVisit(<?php echo $visit['visit_id']; ?>)">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-success save-visit-btn" onclick="saveVisit(<?php echo $visit['visit_id']; ?>)" style="display:none;">
                                                        <i class="fas fa-check"></i> Save
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary cancel-visit-btn" onclick="cancelEditVisit(<?php echo $visit['visit_id']; ?>)" style="display:none;">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Flash messages
    if (isset($_SESSION['form_success'])) {
        $msg = json_encode($_SESSION['form_success']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'success', title: 'Success', text: $msg}); } });</script>";
        unset($_SESSION['form_success']);
    }
    if (isset($_SESSION['form_error'])) {
        $emsg = json_encode($_SESSION['form_error']);
        echo "<script>window.addEventListener('load', function(){ if (typeof Swal !== 'undefined') { Swal.fire({icon: 'error', title: 'Error', text: $emsg}); } });</script>";
        unset($_SESSION['form_error']);
    }
    ?>

</div>

<!-- Dispense Medicine Modal -->
<div class="modal fade" id="dispenseModal" tabindex="-1" aria-labelledby="dispenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dispenseModalLabel">Dispense Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?php echo BASE_URL; ?>?action=medicine-dispense-save">
                <div class="modal-body">
                    <?php echo csrf_input(); ?>
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

                    <div class="mb-3">
                        <label for="medicine_id" class="form-label">Medicine</label>
                        <select id="medicine_id" name="medicine_id" class="form-select" required>
                            <option value="">-- Select medicine --</option>
                            <?php if (!empty($inventory)):
                                $hasStock = false;
                                foreach ($inventory as $item):
                                    $stock = (int)$item['quantity_in_stock'];
                                    $disabled = $stock <= 0 ? 'disabled' : '';
                                    if ($stock > 0) $hasStock = true;
                            ?>
                                    <option value="<?php echo (int)$item['item_id']; ?>" <?php echo $disabled; ?>>
                                        <?php echo htmlspecialchars($item['item_name']); ?>
                                        (Stock: <?php echo $stock; ?>)<?php echo $stock <= 0 ? ' - OUT OF STOCK' : ''; ?>
                                    </option>
                                <?php endforeach;
                                if (!$hasStock): ?>
                                    <option value="" disabled>⚠️ All medicines are out of stock</option>
                                <?php endif; ?>
                            <?php else: ?>
                                <option value="" disabled>No medicines in inventory</option>
                            <?php endif; ?>
                        </select>
                        <?php if (!empty($inventory) && isset($hasStock) && !$hasStock): ?>
                            <small class="text-warning">Please restock medicines in the Inventory section.</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" min="1" value="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <input type="text" id="notes" name="notes" class="form-control" placeholder="e.g., Taken with food">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Dispense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fix Bootstrap modal stacking context issue in admin layout
    // Move modal to body when opened, return when closed
    document.addEventListener('DOMContentLoaded', function() {
        const dispenseModal = document.getElementById('dispenseModal');
        if (dispenseModal) {
            // Store original parent
            const originalParent = dispenseModal.parentNode;

            // Move to body when showing
            dispenseModal.addEventListener('show.bs.modal', function() {
                document.body.appendChild(dispenseModal);
            });

            // Move back when hidden (to maintain form data)
            dispenseModal.addEventListener('hidden.bs.modal', function() {
                if (originalParent) {
                    originalParent.appendChild(dispenseModal);
                }
            });
        }
    });

    // Inline Edit Functions for Health Visits
    function editVisit(visitId) {
        const row = document.getElementById('visit-row-' + visitId);
        if (!row) return;

        // Hide view mode, show edit mode
        row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
        row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'block');

        // Toggle buttons
        row.querySelector('.edit-visit-btn').style.display = 'none';
        row.querySelector('.save-visit-btn').style.display = 'inline-block';
        row.querySelector('.cancel-visit-btn').style.display = 'inline-block';
    }

    function cancelEditVisit(visitId) {
        const row = document.getElementById('visit-row-' + visitId);
        if (!row) return;

        // Show view mode, hide edit mode
        row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'block');
        row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');

        // Toggle buttons
        row.querySelector('.edit-visit-btn').style.display = 'inline-block';
        row.querySelector('.save-visit-btn').style.display = 'none';
        row.querySelector('.cancel-visit-btn').style.display = 'none';
    }

    function saveVisit(visitId) {
        const row = document.getElementById('visit-row-' + visitId);
        if (!row) return;

        const visitDate = row.querySelector('.visit-date-cell .edit-mode').value;
        const visitType = row.querySelector('.visit-type-cell .edit-mode').value;
        const remarks = row.querySelector('.visit-remarks-cell .edit-mode').value;

        if (!visitType) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Visit type is required',
                confirmButtonColor: '#20c997'
            });
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Saving...',
            text: 'Updating visit record',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send AJAX request
        fetch('<?php echo BASE_URL; ?>?action=visit-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    csrf_token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>',
                    visit_id: visitId,
                    visit_date: visitDate,
                    visit_type: visitType,
                    remarks: remarks
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update view mode with new values
                    row.querySelector('.visit-date-cell .view-mode').textContent = visitDate;
                    row.querySelector('.visit-type-cell .view-mode').textContent = visitType;
                    row.querySelector('.visit-remarks-cell .view-mode').textContent = remarks;

                    // Exit edit mode
                    cancelEditVisit(visitId);

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Visit record has been updated',
                        confirmButtonColor: '#20c997',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to update visit',
                        confirmButtonColor: '#20c997'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the visit',
                    confirmButtonColor: '#20c997'
                });
            });
    }

    // Inline Edit Functions for Vitals
    function editVital(vitalId) {
        const row = document.getElementById('vital-row-' + vitalId);
        if (!row) return;

        // Hide view mode, show edit mode
        row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
        row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'block');

        // Toggle buttons
        row.querySelector('.edit-vital-btn').style.display = 'none';
        row.querySelector('.save-vital-btn').style.display = 'inline-block';
        row.querySelector('.cancel-vital-btn').style.display = 'inline-block';
    }

    function cancelEditVital(vitalId) {
        const row = document.getElementById('vital-row-' + vitalId);
        if (!row) return;

        // Show view mode, hide edit mode
        row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'block');
        row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');

        // Toggle buttons
        row.querySelector('.edit-vital-btn').style.display = 'inline-block';
        row.querySelector('.save-vital-btn').style.display = 'none';
        row.querySelector('.cancel-vital-btn').style.display = 'none';
    }

    function saveVital(vitalId) {
        const row = document.getElementById('vital-row-' + vitalId);
        if (!row) return;

        const bloodPressure = row.querySelector('.vital-bp-cell .edit-mode').value;
        const heartRate = row.querySelector('.vital-hr-cell .edit-mode').value;
        const temperature = row.querySelector('.vital-temp-cell .edit-mode').value;
        const notes = row.querySelector('.vital-notes-cell .edit-mode').value;

        // Show loading
        Swal.fire({
            title: 'Saving...',
            text: 'Updating vital signs',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send AJAX request
        fetch('<?php echo BASE_URL; ?>?action=vital-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    csrf_token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>',
                    vital_id: vitalId,
                    blood_pressure: bloodPressure,
                    heart_rate: heartRate,
                    temperature: temperature,
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update view mode with new values
                    row.querySelector('.vital-bp-cell .view-mode').textContent = bloodPressure;
                    row.querySelector('.vital-hr-cell .view-mode').textContent = heartRate;
                    row.querySelector('.vital-temp-cell .view-mode').textContent = temperature;
                    row.querySelector('.vital-notes-cell .view-mode').textContent = notes;

                    // Exit edit mode
                    cancelEditVital(vitalId);

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Vital signs have been updated',
                        confirmButtonColor: '#20c997',
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to update vital signs',
                        confirmButtonColor: '#20c997'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating vital signs',
                    confirmButtonColor: '#20c997'
                });
            });
    }
</script>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>