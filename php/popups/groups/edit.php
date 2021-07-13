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

	define('PAGE', SAG_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	// Make sure we were passed what appear to be calid configuration keys
	if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id']))
	{
		echo invalid_parameters('Edit Group', 'Administration/Groups/Edit.php');
		return;
	}

	// Lookup the group
	$group = lup_group(intval($_REQUEST['id']));

	// Update group if desired
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Save Changes')
	{
		$name = stripslashes($_POST['name']);
		if ($name != stripslashes($group->name))
		{
			if (!empty($name))
			{
				$result = db_update('groups', array('id' => $group->id),
						array('name' => "'" . db_escape($name) . "'"));
				if ($err < ERR_SUCCESS)
					$message = '<span>Failed to update group!</span>';
				else
				{
					$message = '<span>Group updated successfully!</span>';
					$group = lup_group($group->id);
				}
			}
			else
				$message = '<span>Group names cannot be empty!</span>';
		}
	}

	//-------------------------------------------------------------------------
	// Generate display

	$scripts = '';

	$styles = '';

	echo gen_htmlHeader('Edit Group', $styles, $scripts);

	// Add message if specified
	if (isset($message) && !empty($message))
		echo '<div>' . $message . '</div>';

	// Add error message if needed
	if ($err < ERR_SUCCESS)
		echo gen_error();

	if (isset($_POST['name']))
		$temp = $_POST['name'];
	else
		$temp = $group->name;

?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Name</td>
				<td>
					<input type="text" name="name" value="<?php echo $temp ?>" size="20" maxlength="20" />
				</td>
			</tr>
		</table>
		<div>
<?php
	if (allowed(SJ_EDIT))
		$disabled = '';
	else
		$disabled = ' disabled="disabled"';
?>
			<input type="submit" name="action" value="Save Changes"<?php echo $disabled ?> />
		</div>
		<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
	</form>
<?php
	echo gen_htmlFooter();
?>
