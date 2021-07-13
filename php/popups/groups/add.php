<?php
/*
	Copyright 2005, 2006, 2007, 2008 Russell E. Gibson

    This file is part of NewsLedger.

    NewsLedger is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    NewsLedger is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with NewsLedger; see the file LICENSE.  If not, see
	<http://www.gnu.org/licenses/>.
*/

	set_include_path('../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SAG_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	//-------------------------------------------------------------------------
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Add')
	{
		// Add the group
		$sql = "INSERT INTO `groups` SET `name` = " . db_escape($_REQUEST['name']);

		$id = db_insert('groups', array('name' => "'" . db_escape($_POST['name']) . "'"));
		if ($err < ERR_SUCCESS)
			$message = '<span>Failed to add group!</span>';
		else
			$message = '<span>Created new group ' . sprintf('G%04d', $id) . '!</span>';
	}

	//-------------------------------------------------------------------------
	$scripts = '';

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------

	echo gen_htmlHeader('Add Group', $styles, $scripts);

	// Add message if specified
	if (isset($message) && !empty($message))
		echo '<div>' . $message . '</div>';

	// Add error message if needed
	if ($err < ERR_SUCCESS)
		echo gen_error();

?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Name</td>
				<td>
<?php
	if (isset($_REQUEST['name']) && !empty($_REQUEST['name']))
		$val = stripslashes($_REQUEST['name']);
	else
		$val = '';
?>
					<input type="text" name="name" value="<?php echo $val ?>" size="20" maxlength="20" />
				</td>
			</tr>
		</table>
		<div>
			<input type="submit" name="action" value="Add" />
		</div>
	</form>
<?php
	echo gen_htmlFooter();
?>
