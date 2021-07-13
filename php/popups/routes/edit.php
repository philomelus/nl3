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

    define('ROOT', '../../');

    set_include_path(ROOT . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SAR_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	if (!isset($_REQUEST['id']))
	{
		echo invalid_parameters('Edit Route', 'Administration/Routes/Edit.php');
		return;
	}

	$route = lup_route(intval($_REQUEST['id']));

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Save Changes')
	{
		$query = "UPDATE `routes` SET";
		$comma = '';
		if (stripslashes($_REQUEST['title']) != stripslashes($route->title))
		{
			$query .= $comma . " `title` = '" . db_escape(stripslashes($_REQUEST['title'])) . "'";
			$comma = ',';
		}
		if ($_POST['active'] != $route->active)
		{
			$query .= $comma . ' `active` = \'' . db_escape($_POST['active']) . '\'';
			$comma = ',';
		}
		if ($comma == ',')
		{
			$query .= " WHERE `id` = " . $route->id . " LIMIT 1";
			$result = db_query($query);
			if (!$result)
				$message = '<span>Failed to update route!</span>';
			else
				$message = '<span>Route updated successfully!</span>';
		}
	}

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript">
</script>
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Edit Route', $styles, $script);

	// Add message if specified
	if (isset($message) && !empty($message))
	{
?>
		<div><?php echo $message ?></div>
<?php
	}

	// Add error message if needed
	if ($err < ERR_SUCCESS)
		echo gen_error();

?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Title</td>
				<td>
					<input type="text" name="title" value="<?php echo $route->title ?>" size="20" maxlength="20" />
				</td>
			</tr>
			<tr>
				<td>Active</td>
				<td>
<?php
	$val = array('Y' => '', 'N' => '');
	if (isset($_POST['active']))
	{
		if ($_POST['active'] == 'Y')
			$val['Y'] = ' checked="checked"';
		else
			$val['N'] = ' checked="checked"';
	}
	else
	{
		if ($route->active == 'Y')
			$val['Y'] = ' checked="checked"';
		else
			$val['N'] = ' checked="checked"';
	}
?>
					<input type="radio" name="active" value="Y"<?php echo $val['Y'] ?>>Yes</input>
					<input type="radio" name="active" value="N"<?php echo $val['N'] ?>>No</input>
				</td>
			</tr>
		</table>
		<div>
			<input type="submit" name="action" value="Save Changes" />
		</div>
		<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
	</form>
<?php
	echo gen_htmlFooter();
?>
