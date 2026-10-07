<?php
require_once 'db.php';
$pageTitle = 'Equipment';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $equipmentId = trim($_POST['equipment_id'] ?? '');
        $name = trim($_POST['equipment_name'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $status = $_POST['status'] ?? 'Operational';
        $last = $_POST['last_maintenance'] ?: null;
        $next = $_POST['next_maintenance'] ?: null;
        if ($equipmentId === '' || $name === '' || $department === '') $error = 'Please fill all required fields.';
        elseif ($next && $last && $next < $last) $error = 'Next maintenance date cannot be before last maintenance date.';
        else {
            $stmt = $conn->prepare("INSERT INTO equipment (equipment_id,equipment_name,department,status,last_maintenance,next_maintenance) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $equipmentId,$name,$department,$status,$last,$next);
            if ($stmt->execute()) $message = 'Equipment added successfully.'; else $error = 'Could not add equipment. Equipment ID may already exist.';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare('DELETE FROM equipment WHERE id=?'); $stmt->bind_param('i',$id);
        if ($stmt->execute()) $message = 'Equipment deleted successfully.'; else $error = 'Could not delete equipment.';
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0); $equipmentId = trim($_POST['equipment_id'] ?? ''); $name = trim($_POST['equipment_name'] ?? ''); $department = trim($_POST['department'] ?? ''); $status = $_POST['status'] ?? 'Operational'; $last = $_POST['last_maintenance'] ?: null; $next = $_POST['next_maintenance'] ?: null;
        if ($equipmentId === '' || $name === '' || $department === '') $error = 'Please fill all required fields.';
        elseif ($next && $last && $next < $last) $error = 'Next maintenance date cannot be before last maintenance date.';
        else { $stmt=$conn->prepare('UPDATE equipment SET equipment_id=?,equipment_name=?,department=?,status=?,last_maintenance=?,next_maintenance=? WHERE id=?'); $stmt->bind_param('ssssssi',$equipmentId,$name,$department,$status,$last,$next,$id); if($stmt->execute()) $message='Equipment updated successfully.'; else $error='Could not update equipment.'; }
    }
}
$edit = null;
if (isset($_GET['edit'])) { $id=(int)$_GET['edit']; $stmt=$conn->prepare('SELECT * FROM equipment WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $edit=$stmt->get_result()->fetch_assoc(); }
$search = trim($_GET['search'] ?? '');
if ($search !== '') { $like="%$search%"; $stmt=$conn->prepare('SELECT * FROM equipment WHERE equipment_id LIKE ? OR equipment_name LIKE ? OR department LIKE ? OR status LIKE ? ORDER BY id DESC'); $stmt->bind_param('ssss',$like,$like,$like,$like); $stmt->execute(); $equipment=$stmt->get_result(); }
else $equipment=$conn->query('SELECT * FROM equipment ORDER BY id DESC');
require 'includes/header.php';
?>
<section class="page-head"><div><p class="eyebrow">ASSET REGISTER</p><h1>Equipment</h1><p>Add, update, search, and remove industrial equipment records.</p></div><a class="btn primary" href="equipment.php?action=add">+ Add Equipment</a></section>
<?php if($message): ?><div class="alert success"><?= htmlspecialchars($message) ?></div><?php endif; ?><?php if($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if(isset($_GET['action']) && $_GET['action']==='add' || $edit): ?>
<section class="panel form-panel"><div class="panel-head"><div><h2><?= $edit ? 'Edit Equipment' : 'Add Equipment' ?></h2></div><a class="text-link" href="equipment.php">Cancel</a></div>
<form method="post" class="form-grid"><input type="hidden" name="action" value="<?= $edit?'edit':'add' ?>"><?php if($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
<label>Equipment ID *<input name="equipment_id" required value="<?= htmlspecialchars($edit['equipment_id'] ?? '') ?>" placeholder="EQ-006"></label>
<label>Equipment Name *<input name="equipment_name" required value="<?= htmlspecialchars($edit['equipment_name'] ?? '') ?>" placeholder="CNC Machine"></label>
<label>Department *<input name="department" required value="<?= htmlspecialchars($edit['department'] ?? '') ?>" placeholder="Production"></label>
<label>Status<select name="status"><option <?= (($edit['status'] ?? '')==='Operational')?'selected':'' ?>>Operational</option><option <?= (($edit['status'] ?? '')==='Under Maintenance')?'selected':'' ?>>Under Maintenance</option><option <?= (($edit['status'] ?? '')==='Critical')?'selected':'' ?>>Critical</option></select></label>
<label>Last Maintenance<input type="date" name="last_maintenance" value="<?= htmlspecialchars($edit['last_maintenance'] ?? '') ?>"></label>
<label>Next Maintenance<input type="date" name="next_maintenance" value="<?= htmlspecialchars($edit['next_maintenance'] ?? '') ?>"></label>
<div class="form-actions"><button class="btn primary" type="submit"><?= $edit?'Update Equipment':'Save Equipment' ?></button><a class="btn" href="equipment.php">Cancel</a></div></form></section>
<?php endif; ?>
<section class="panel"><div class="panel-head"><div><h2>Equipment Records</h2><p><?= $equipment ? $equipment->num_rows : 0 ?> record(s) from MySQL database.</p></div><form class="search" method="get"><input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search equipment..."><button class="btn" type="submit">Search</button></form></div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Equipment</th><th>Department</th><th>Status</th><th>Last</th><th>Next</th><th>Actions</th></tr></thead><tbody>
<?php if($equipment && $equipment->num_rows): while($row=$equipment->fetch_assoc()): ?><tr><td><?= htmlspecialchars($row['equipment_id']) ?></td><td><strong><?= htmlspecialchars($row['equipment_name']) ?></strong></td><td><?= htmlspecialchars($row['department']) ?></td><td><span class="status <?= strtolower(str_replace(' ','-', $row['status'])) ?>"><?= htmlspecialchars($row['status']) ?></span></td><td><?= htmlspecialchars($row['last_maintenance'] ?? '-') ?></td><td><?= htmlspecialchars($row['next_maintenance'] ?? '-') ?></td><td class="actions"><a class="small-btn" href="equipment.php?edit=<?= (int)$row['id'] ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this equipment? Related maintenance records will also be deleted.');"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><button class="small-btn danger" type="submit">Delete</button></form></td></tr><?php endwhile; else: ?><tr><td colspan="7" class="empty">No equipment records found.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require 'includes/footer.php'; ?>
