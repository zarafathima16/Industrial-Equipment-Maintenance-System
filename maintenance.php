<?php
require_once 'db.php';
$pageTitle='Maintenance'; $message=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';
    if($action==='add'){
        $equipmentId=trim($_POST['equipment_id']??''); $type=trim($_POST['maintenance_type']??''); $date=$_POST['scheduled_date']??''; $tech=trim($_POST['technician']??''); $desc=trim($_POST['description']??'');
        if(!$equipmentId||!$type||!$date||!$tech) $error='Please fill all required fields.';
        else { $stmt=$conn->prepare('INSERT INTO maintenance (equipment_id,maintenance_type,scheduled_date,technician,description) VALUES (?,?,?,?,?)'); $stmt->bind_param('sssss',$equipmentId,$type,$date,$tech,$desc); if($stmt->execute()) $message='Maintenance scheduled successfully.'; else $error='Could not schedule maintenance. Check the equipment ID.'; }
    } elseif($action==='delete') { $id=(int)($_POST['id']??0); $stmt=$conn->prepare('DELETE FROM maintenance WHERE id=?'); $stmt->bind_param('i',$id); if($stmt->execute()) $message='Maintenance record deleted.'; else $error='Could not delete record.'; }
}
$equipments=$conn->query('SELECT equipment_id,equipment_name FROM equipment ORDER BY equipment_name');
$search=trim($_GET['search']??'');
if($search!==''){ $like="%$search%"; $stmt=$conn->prepare('SELECT m.*,e.equipment_name FROM maintenance m LEFT JOIN equipment e ON e.equipment_id=m.equipment_id WHERE m.equipment_id LIKE ? OR e.equipment_name LIKE ? OR m.maintenance_type LIKE ? OR m.technician LIKE ? ORDER BY m.scheduled_date ASC'); $stmt->bind_param('ssss',$like,$like,$like,$like); $stmt->execute(); $records=$stmt->get_result(); } else $records=$conn->query('SELECT m.*,e.equipment_name FROM maintenance m LEFT JOIN equipment e ON e.equipment_id=m.equipment_id ORDER BY m.scheduled_date ASC');
require 'includes/header.php';
?>
<section class="page-head"><div><p class="eyebrow">PLANNED SERVICE</p><h1>Maintenance</h1><p>Schedule and manage preventive and corrective maintenance.</p></div><a class="btn primary" href="maintenance.php?action=add">+ Schedule Maintenance</a></section>
<?php if($message): ?><div class="alert success"><?= htmlspecialchars($message) ?></div><?php endif; ?><?php if($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if(isset($_GET['action']) && $_GET['action']==='add'): ?>
<section class="panel form-panel"><div class="panel-head"><div><h2>Schedule Maintenance</h2></div><a class="text-link" href="maintenance.php">Cancel</a></div>
<?php if(!$equipments || $equipments->num_rows===0): ?><div class="empty">Add equipment first before scheduling maintenance.</div><?php else: ?><form method="post" class="form-grid"><input type="hidden" name="action" value="add">
<label>Equipment *<select name="equipment_id" required><option value="">Select equipment</option><?php while($e=$equipments->fetch_assoc()): ?><option value="<?= htmlspecialchars($e['equipment_id']) ?>"><?= htmlspecialchars($e['equipment_id'].' — '.$e['equipment_name']) ?></option><?php endwhile; ?></select></label>
<label>Maintenance Type *<input name="maintenance_type" required placeholder="Preventive Maintenance"></label>
<label>Scheduled Date *<input type="date" name="scheduled_date" required></label>
<label>Technician *<input name="technician" required placeholder="Technician name"></label>
<label class="full">Description<textarea name="description" rows="4" placeholder="Describe the maintenance task..."></textarea></label>
<div class="form-actions"><button class="btn primary" type="submit">Schedule Maintenance</button><a class="btn" href="maintenance.php">Cancel</a></div></form><?php endif; ?></section>
<?php endif; ?>
<section class="panel"><div class="panel-head"><div><h2>Maintenance Records</h2><p><?= $records ? $records->num_rows : 0 ?> record(s) from MySQL database.</p></div><form class="search" method="get"><input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search maintenance..."><button class="btn" type="submit">Search</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Equipment</th><th>Type</th><th>Scheduled</th><th>Technician</th><th>Description</th><th>Action</th></tr></thead><tbody>
<?php if($records && $records->num_rows): while($row=$records->fetch_assoc()): ?><tr><td><strong><?= htmlspecialchars($row['equipment_name'] ?? $row['equipment_id']) ?></strong><small><?= htmlspecialchars($row['equipment_id']) ?></small></td><td><?= htmlspecialchars($row['maintenance_type']) ?></td><td><?= htmlspecialchars($row['scheduled_date']) ?></td><td><?= htmlspecialchars($row['technician']) ?></td><td><?= htmlspecialchars($row['description'] ?: '-') ?></td><td><form method="post" onsubmit="return confirm('Delete this maintenance record?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button class="small-btn danger" type="submit">Delete</button></form></td></tr><?php endwhile; else: ?><tr><td colspan="6" class="empty">No maintenance records found.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require 'includes/footer.php'; ?>
