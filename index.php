<?php
include 'template.php';

$template = new Template($db);
?>


<?=$template->home_header_template('Home')?>
    <div class="home-bg-img m-0">
    </div>
<?=$template->footer_template('Home')?>
