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

	define('PAGE', SAU_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	//-------------------------------------------------------------------------
	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['id']))
	{
		echo invalid_parameters('Edit User', 'Administration/Users/Edit.php');
		return;
	}

	//-------------------------------------------------------------------------

	$UID = intval($_REQUEST['id']);
	$user = lup_user($UID);

	//-------------------------------------------------------------------------
	if (isset($_POST['action']) && $_POST['action'] == 'Save Changes')
	{
		$updates = array();
		if ($_POST['gid'] != $user->group_id)
			$updates['group_id'] = intval($_POST['gid']);
		$temp = stripslashes($_POST['name']);
		if ($temp != stripslashes($user->name))
			$updates['name'] = "'" . db_escape($temp) . "'";
		$temp = stripslashes($_POST['login']);
		if ($temp != stripslashes($user->login))
			$updates['login'] = "'" . db_escape($temp) . "'";
		if (!empty($_POST['pwnew']))
		{
			$temp = md5(stripslashes($_POST['pwnew']));
			if ($temp == md5(stripslashes($_POST['pwagain'])))
			{
				if ($temp != $user->password)
					$updates['password'] = "'" . db_escape($temp) . "'";
			}
			else
			{
				$err = ERR_FAILURE;
				$errCode = ERR_FAILURE;
				$errContext = 'Updating user';
				$errQuery = '';
				$errText = 'New passwords don\'t match!';
				$message = '<span>Update failed!</span>';
			}
		}
		if (count($updates) > 0)
		{
			if ($err >= ERR_SUCCESS)
			{
				$result = db_update('users', array('id' => $user->id), $updates);
				if ($result)
				{
					$message = '<span>User updated successfully.</span>';
					$user = lup_user($user->id);
				}
				else
				{
					$errContext = 'Updating user';
					$message = '<span>User update failed!</span>"';
				}
			}
			else
				$message = '<span>Update failed!</span>';
		}
		else if (empty($message))
			$message = '<span>No changes needed saving.</span>';
	}

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript">
</script>
';

//-----------------------------------------------------------------------------
	$styles =
'
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Edit User', $styles, $script);

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
				<td>Group</td>
				<td>
					<select name="gid">
<?php
	$GROUPS = array();
	$query = "SELECT * FROM `groups` ORDER BY `name`";
	$groups = db_query($query);
	if ($groups)
	{
		if (isset($_REQUEST['gid']) && !empty($_REQUEST['gid']))
			$gid = intval($_REQUEST['gid']);
		else
			$gid = $user->group_id;
		while ($group = $groups->fetch_object())
		{
			if ($group->id == $gid)
				$selected = ' selected="selected"';
			else
				$selected = '';
			echo '<option value="' . $group->id . '"' . $selected . '>' . $group->name . '</option>';
		}
	}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Name</td>
				<td>
<?php
	if (isset($_REQUEST['name']) && !empty($_REQUEST['name']))
		$val = stripslashes($_REQUEST['name']);
	else
		$val = stripslashes($user->name);
?>
					<input type="text" name="name" value="<?php echo $val ?>" size="30" maxlength="30" />
				</td>
			</tr>
			<tr>
				<td>Login</td>
				<td>
<?php
	if (isset($_REQUEST['login']) && !empty($_REQUEST['login']))
		$val = stripslashes($_REQUEST['login']);
	else
		$val = stripslashes($user->login);
?>
					<input type="text" name="login" value="<?php echo $val ?>" size="20" maxlength="20" />
				</td>
			</tr>
			<tr>
				<td>New Password</td>
				<td>
<?php
	if (isset($_POST['pwnew']))
		$val = stripslashes($_POST['pwnew']);
	else
		$val = '';
?>
					<input type="password" name="pwnew" value="<?php echo $val ?>" size="20" maxlength="20" />
				</td>
			</tr>
			<tr>
				<td>(again)</td>
				<td>
					<input type="password" name="pwagain" value="" size="20" maxlength="20" />
				</td>
			</tr>
		</table>
		<div>
<?php
	if (allowed(SS_EDIT))
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
