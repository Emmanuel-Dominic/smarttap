<?php
include '../template.php';

$template = new Template($db);
$template->session_check('admin');

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$record_id = 0;

$stmt = $db->prepare('SELECT * FROM users WHERE role="admin" LIMIT :current_page, :record_per_page');
$stmt->bindValue(':current_page', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$num_users = $db->query('SELECT COUNT(*) FROM users WHERE role="admin"')->fetchColumn();
?>

<?=$template->dashboard_header_template('Admins', $_SESSION['name'], $_SESSION['role'])?>
<div class="container">
	<div class="content read">
		<h2>Administrators</h2>
		<a href="adduser.php" class="btn btn-sm btn-primary btn-icon-split float-right">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-down"></i>
            </span>
            <span class="text">Add Admin</span>
        </a>
        <br>
        <br>
		<table>
		    <thead>
		        <tr>
		            <td>#</td>
		            <td>Name</td>
		            <td>Email</td>
		            <td>Phone</td>
		            <td>Location</td>
		            <td>Role</td>
		            <td>CreatedAt</td>
		            <td>Action</td>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach ($users as $user): ?>
		        	<?php $record_id++ ?>
		        <tr>
		            <td><?=$record_id?></td>
		            <td><?=$user['name']?></td>
		            <td><?=$user['email']?></td>
		            <td><?=$user['phone']?></td>
		            <td><?=$user['location']?></td>
		            <td><?=$user['role']?></td>
		            <td><?=$user['created_at']?></td>
		            <td class="">
                       	<a href="delete_user.php?id=<?=$user['id']?>" class="btn btn-sm btn-danger btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-trash"></i>
                            </span>
                            <span class="text">Delete</span>
                        </a>
		            </td>
		        </tr>
		        <?php endforeach; ?>
		    </tbody>
		</table>
		<div class="pagination">
			<?php if ($page > 1): ?>
			<a href="users.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
			<?php endif; ?>
			<?php if ($page*$records_per_page < $num_users): ?>
			<a href="users.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
			<?php endif; ?>
		</div>
	</div>	
</div>
<?=$template->footer_template('Dashboard')?>
