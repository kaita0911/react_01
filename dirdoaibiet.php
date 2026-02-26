<?php
@session_start();
if (!$_SESSION['shoptinhnhan'])
	$_SESSION['shoptinhnhan'] = 'shoptinhnhan';
header('Location:/admindir/index.php');
