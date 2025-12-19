<?php
/**
 * Medicine Dispensation History - Admin Page
 * Shows all medicine dispensation logs with patient details
 */
include_once __DIR__ . '/../../includes/header_admin.php';

// Fetch all dispensation logs with patient and medicine info
$query = "
    SELECT 
        mdl.*,
        mi.item_name as medicine_name,
        p.full_name as patient_name,
        bu.full_name as bhw_name
    FROM medicine_dispensing_log mdl
    LEFT JOIN medication_inventory mi ON mdl.item_id = mi.item_id
    LEFT JOIN patients p ON mdl.resident_id = p.patient_id
    LEFT JOIN bhw_users bu ON mdl.bhw_id = bu.bhw_id
    ORDER BY mdl.dispensed_at DESC
    LIMIT 100
";
$stmt = $pdo->query($query);
$dispensations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Medicine Dispensation History</h1>
        <p class="page-subtitle">Track all medicine dispensations to patients</p>
    </div>
</div>

<div class="glass-card table-container">
    <?php if (!empty($dispensations)): ?>
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Patient</th>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Dispensed By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dispensations as $d): ?>
                    <tr>
                        <td><?php echo date('M j, Y g:i A', strtotime($d['dispensed_at'])); ?></td>
                        <td>
                            <div class="item-cell">
                                <span class="item-name"><?php echo htmlspecialchars($d['patient_name'] ?? 'Unknown Patient'); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge active"><?php echo htmlspecialchars($d['medicine_name'] ?? 'Unknown'); ?></span>
                        </td>
                        <td><strong><?php echo (int)$d['quantity']; ?></strong></td>
                        <td><?php echo htmlspecialchars($d['bhw_name'] ?? 'N/A'); ?></td>
                        <td class="description-cell"><?php echo htmlspecialchars($d['notes'] ?: '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">💊</div>
            <h4>No Dispensation Records</h4>
            <p>Medicine dispensation history will appear here once medicines are dispensed to patients.</p>
        </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer_admin.php'; ?>
