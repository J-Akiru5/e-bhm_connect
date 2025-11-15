<?php
// Protected: View all patients
// Authentication enforced by the central router (index.php)
include_once __DIR__ . '/../../includes/header_admin.php';

// Fetch patients from database
require_once __DIR__ . '/../../config/database.php';

$patients = [];
try {
	$stmt = $pdo->query('SELECT * FROM patients ORDER BY full_name ASC');
	$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
	error_log('Patients query error: ' . $e->getMessage());
}

// Show form success/error messages via SweetAlert2 if present
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

<div class="d-flex justify-content-between align-items-center mb-3">
	<h1>Patient Records</h1>
	<a href="/e-bmw_connect/admin-patient-form" class="btn btn-success mb-3">Add New Patient</a>
</div>

<?php if (empty($patients)): ?>
	<div class="alert alert-info">No patients found. Click 'Add New Patient' to get started.</div>
<?php else: ?>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Address</th>
					<th>Birthdate</th>
					<th>Sex</th>
					<th>Contact</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($patients as $patient): ?>
					<tr>
						<td><?php echo htmlspecialchars($patient['full_name'] ?? ''); ?></td>
						<td><?php echo htmlspecialchars($patient['address'] ?? ''); ?></td>
						<td><?php echo htmlspecialchars($patient['birthdate'] ?? ''); ?></td>
						<td><?php echo htmlspecialchars($patient['sex'] ?? ''); ?></td>
						<td><?php echo htmlspecialchars($patient['contact'] ?? ''); ?></td>
						<td>
							<a href="/e-bmw_connect/admin-patient-view?id=<?php echo urlencode($patient['patient_id'] ?? ''); ?>" class="btn btn-primary btn-sm">View</a>
							<a href="/e-bmw_connect/admin-patient-form?id=<?php echo urlencode($patient['patient_id'] ?? ''); ?>" class="btn btn-secondary btn-sm">Edit</a>

							<form action="?action=delete-patient" method="POST" class="d-inline" onsubmit="return confirmDelete(event);">
								<input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient['patient_id'] ?? ''); ?>">
								<button type="submit" class="btn btn-danger btn-sm">Delete</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/footer_admin.php';
