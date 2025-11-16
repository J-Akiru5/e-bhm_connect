<?php
$stmt = $pdo->query("SELECT a.*, b.full_name 
                    FROM announcements a 
                    LEFT JOIN bhw_users b ON a.bhw_id = b.bhw_id 
                    ORDER BY a.created_at DESC");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once __DIR__ . '/../../includes/header_public.php';
?>

<div class="container py-5">
    <h1 class="display-5 fw-bold text-center mb-5">Announcements</h1>

    <?php if (empty($announcements)): ?>
        <div class="alert alert-info">No announcements at this time.</div>
    <?php else: ?>
        <?php foreach ($announcements as $announcement): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header">
                    <?php echo htmlspecialchars($announcement['title'] ?? ''); ?>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($announcement['content'] ?? '')); ?></p>
                </div>
                <div class="card-footer text-muted">
                    <small>Posted on <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?> by <?php echo htmlspecialchars($announcement['full_name'] ?? 'System'); ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?php
include_once __DIR__ . '/../../includes/footer_public.php';
?>
