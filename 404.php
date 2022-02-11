<?php
include 'templates.php';

$template = new Template($db);
?>

<?=$template->home_header_template('Not Found!')?>
	<div class="content-view text-center">
	    <div class="error mx-auto" data-text="404">404</div>
	    <p class="lead text-gray-800 mb-5">Page Not Found</p>
	    <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
	    <a href="index.php">&larr; Back to index page</a>
	</div>
<?=$template->footer_template("home")?>
