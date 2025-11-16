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

<script>
document.addEventListener('DOMContentLoaded', () => {
	const ctx = document.getElementById('health-issues-chart').getContext('2d');
	const yearFilter = document.getElementById('year-filter');
	let myChart;

	myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: [],
			datasets: [{
				label: '# of Visits',
				data: [],
				backgroundColor: 'rgba(0, 123, 255, 0.5)',
				borderColor: 'rgba(0, 123, 255, 1)',
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true,
					ticks: {
						precision: 0
					}
				}
			},
			responsive: true,
			maintainAspectRatio: false
		}
	});

	async function updateChart(year) {
		myChart.data.labels = ['Loading...'];
		myChart.data.datasets[0].data = [];
		myChart.update();

		const response = await fetch(`<?php echo BASE_URL; ?>?action=get-chart-data&year=${year}`);
		const chartData = await response.json();

		myChart.data.labels = chartData.labels;
		myChart.data.datasets[0].data = chartData.data;
		myChart.update();
	}

	yearFilter.addEventListener('change', () => {
		updateChart(yearFilter.value);
	});

	updateChart('');
});
</script>
