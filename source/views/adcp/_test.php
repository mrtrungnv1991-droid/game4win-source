<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$body=['title'=>'Test'];
require_once(__DIR__.'/../admin/header.php');
require_once(__DIR__.'/../admin/sidebar.php');
echo '<div class="main-content app-content"><h1>TEST OK</h1></div>';
require_once(__DIR__.'/../admin/footer.php');
