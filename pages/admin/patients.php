<?php
// Protected: View all patients
// Authentication enforced by the central router (index.php)
include_once __DIR__ . '/../../includes/header_admin.php';
require_once __DIR__ . '/../../includes/pagination_helper.php';

// Fetch patients from database
require_once __DIR__ . '/../../config/database.php';

$patients = [];
$pagination = ['current_page' => 1, 'total_pages' => 1, 'total_records' => 0];
$per_page = 10;

try {
	// Count total records first
	$count_sql = "SELECT COUNT(*) FROM patients";
	$params = [];

	if (!empty($_GET['search'])) {
		$search_term = '%' . $_GET['search'] . '%';
		$count_sql .= " WHERE full_name LIKE ?";
		$params[] = $search_term;
	}

	$count_stmt = $pdo->prepare($count_sql);
	$count_stmt->execute($params);
	$total_records = (int) $count_stmt->fetchColumn();

	// Calculate pagination
	$current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
	$pagination = paginate($total_records, $per_page, $current_page);

	// Base SQL query with LIMIT
	$sql = "SELECT * FROM patients";
	$params = [];

	if (!empty($_GET['search'])) {
		$search_term = '%' . $_GET['search'] . '%';
		$sql .= " WHERE full_name LIKE ?";
		$params[] = $search_term;
	}

	$sql .= " ORDER BY full_name ASC LIMIT " . $pagination['per_page'] . " OFFSET " . $pagination['offset'];

	$stmt = $pdo->prepare($sql);
	$stmt->execute($params);
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

<style>
	.stat-card { transition: transform .18s ease, box-shadow .18s ease; }
	.stat-card:hover{ transform: translateY(-6px); box-shadow: 0 12px 30px rgba(16,24,32,0.06); }
	.table thead th{ color:#495057; font-weight:600; }
</style>
<div class="card mb-3 stat-card">
	<div class="card-body">
		<h4 class="card-title">Search Patient</h4>
		<form method="GET" action="">
			<div class="input-group">
				<input type="text" class="form-control" name="search" placeholder="Search by patient name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
				<button class="btn btn-primary" type="submit">Search</button>
				<a href="<?php echo BASE_URL; ?>admin-patients" class="btn btn-outline-secondary">Clear</a>
			</div>
		</form>
	</div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="h4">Patient Records</h2>
	<a href="<?php echo BASE_URL; ?>admin-patient-form" class="btn btn-success mb-3">Add New Patient</a>
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
							<a href="<?php echo BASE_URL; ?>admin-patient-view?id=<?php echo urlencode($patient['patient_id'] ?? ''); ?>" class="btn btn-primary btn-sm">View</a>
							<a href="<?php echo BASE_URL; ?>admin-patient-form?id=<?php echo urlencode($patient['patient_id'] ?? ''); ?>" class="btn btn-secondary btn-sm">Edit</a>

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
	
	<!-- Pagination -->
	<div class="d-flex justify-content-between align-items-center mt-3">
		<?php echo render_pagination($pagination, get_pagination_base_url()); ?>
	</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/footer_admin.php';
