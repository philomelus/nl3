<?php

    require_once 'inc/security.inc.php';

    define('PAGE', S_MAINTENANCE);

    require_once 'inc/database.inc.php';
    require_once 'inc/audit.inc.php';

    $title = get_globalConfig('default-title', 'NewsLedger');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=cp1252" />
    <title><?php echo $title; ?></title>
</head>
<body>
    <div><?php echo $title; ?></div>
    <h1>Server Maintenance In Progress</h1>
    <p>Please try again later.</p>
<?php
    echo gen_htmlHeader();

    // Prevent parent from executing
    exit();
?>
