<?php
// (Security check is done by index.php)
// (Database $pdo is provided by index.php)

// --- 1. Fetch Stat Card Data ---
$stmt_patients = $pdo->query("SELECT COUNT(*) FROM patients");
$total_patients = $stmt_patients->fetchColumn();

$stmt_bhw = $pdo->query("SELECT COUNT(*) FROM bhw_users");
$total_bhw = $stmt_bhw->fetchColumn();

$stmt_visits = $pdo->query("SELECT COUNT(*) FROM health_visits");
$total_visits = $stmt_visits->fetchColumn();

$stmt_items = $pdo->query("SELECT COUNT(*) FROM medication_inventory");
$total_items = $stmt_items->fetchColumn();

// --- 2. Fetch Years for Filter Dropdown ---
$stmt_years = $pdo->query("SELECT DISTINCT YEAR(visit_date) as year FROM health_visits ORDER BY year DESC");
$available_years = $stmt_years->fetchAll();

// Include the header
include_once __DIR__ . '/../../includes/header_admin.php';
?>

<div class="container-fluid">
	<h1 class="h3 mb-3">Admin Dashboard</h1>

	<div class="row g-4 mb-4">
		<div class="col-md-3">
			<div class="card text-white bg-primary shadow">
				<div class="card-body">
					<div class="fs-3 fw-bold"><?php echo $total_patients; ?></div>
					<div>Total Patients</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-white bg-success shadow">
				<div class="card-body">
					<div class="fs-3 fw-bold"><?php echo $total_bhw; ?></div>
					<div>BHW Users</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-white bg-info shadow">
				<div class="card-body">
					<div class="fs-3 fw-bold"><?php echo $total_visits; ?></div>
					<div>Health Visits Recorded</div>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card text-white bg-warning shadow">
				<div class="card-body">
					<div class="fs-3 fw-bold"><?php echo $total_items; ?></div>
					<div>Inventory Items</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="card shadow">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h5 class="card-title mb-0">Health Visit Statistics</h5>
                    
					<div class="col-md-3">
						<label for="year-filter" class="form-label-sm">Filter by Year:</label>
						<select id="year-filter" class="form-select">
							<option value="">All Years</option>
							<?php foreach ($available_years as $row): ?>
								<option value="<?php echo $row['year']; ?>">
									<?php echo $row['year']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="card-body">
					<canvas id="health-issues-chart"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
// Include the footer
include_once __DIR__ . '/../../includes/footer_admin.php';
?>
