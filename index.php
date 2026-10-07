<?php
require_once 'db.php';
$pageTitle = 'Dashboard';
$total = $operational = $underMaintenance = $critical = 0;
$result = $conn->query("SELECT COUNT(*) total, SUM(status='Operational') operational, SUM(status='Under Maintenance') under_maintenance, SUM(status='Critical') critical FROM equipment");
if ($result) {
    $stats = $result->fetch_assoc();
    $total = (int)($stats['total'] ?? 0);
    $operational = (int)($stats['operational'] ?? 0);
    $underMaintenance = (int)($stats['under_maintenance'] ?? 0);
    $critical = (int)($stats['critical'] ?? 0);
}
$upcoming = $conn->query("SELECT m.*, e.equipment_name FROM maintenance m LEFT JOIN equipment e ON e.equipment_id=m.equipment_id ORDER BY m.scheduled_date ASC LIMIT 5");
require 'includes/header.php';
?>
<section class="hero">
    <div><p class="eyebrow">INDUSTRIAL OPERATIONS</p><h1>Maintenance at a glance.</h1><p>Track equipment health, schedule maintenance, and keep your operations running smoothly.</p></div>
    <div class="hero-actions"><a class="btn primary" href="equipment.php?action=add">+ Add Equipment</a><a class="btn" href="maintenance.php?action=add">Schedule Maintenance</a></div>
</section>
<section class="kpi-grid">
    <div class="kpi"><span>Total Equipment</span><strong><?= $total ?></strong></div>
    <div class="kpi"><span>Operational</span><strong><?= $operational ?></strong></div>
    <div class="kpi"><span>Under Maintenance</span><strong><?= $underMaintenance ?></strong></div>
    <div class="kpi"><span>Critical</span><strong><?= $critical ?></strong></div>
</section>
<section class="panel">
    <div class="panel-head"><div><h2>Upcoming Maintenance</h2><p>Next scheduled maintenance activities from MySQL.</p></div><a class="text-link" href="maintenance.php">View all →</a></div>
    <?php if ($upcoming && $upcoming->num_rows): ?>
    <div class="table-wrap"><table><thead><tr><th>Equipment</th><th>Type</th><th>Date</th><th>Technician</th></tr></thead><tbody>
    <?php while ($row = $upcoming->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($row['equipment_name'] ?? $row['equipment_id']) ?></td><td><?= htmlspecialchars($row['maintenance_type']) ?></td><td><?= htmlspecialchars($row['scheduled_date']) ?></td><td><?= htmlspecialchars($row['technician']) ?></td></tr>
    <?php endwhile; ?>
    </tbody></table></div>
    <?php else: ?><div class="empty">No maintenance records yet. <a href="maintenance.php?action=add">Schedule the first one.</a></div><?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
