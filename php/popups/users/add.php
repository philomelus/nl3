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

	define('PAGE', SAU_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	//-------------------------------------------------------------------------
	if (isset($_POST['action']) && $_POST['action'] == 'Add')
	{
		// Build field list
		$fields = array();
		$fields['group_id'] = intval($_POST['gid']);
		$fields['name'] = "'" . db_escape(stripslashes($_POST['name'])) . "'";
		$fields['login'] = "'" . db_escape(stripslashes($_POST['login'])) . "'";
		if (md5($_POST['pwnew']) == md5($_POST['pwagain']))
			$fields['password'] = "'" . md5($_POST['pwnew']) . "'";
		else
		{
			$err = ERR_FAILURE;
			$errCode = ERR_FAILURE;
			$errContext = 'Updating user';
			$errQuery = '';
			$errText = 'New passwords don\'t match!';
		}

		// Add user if everything is ok
		if ($err >= ERR_SUCCESS)
		{
			$result = db_insert('users', $fields);
			if (!$result)
				$message = '<span>User addition failed!</span>"';
			else
			{
				$message = '<span>User addded successfully as '
						. sprintf('U%04d', $result) . '!</span>';
			}
		}
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

	echo gen_htmlHeader('Add User', $styles, $script);

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
			$gid = 0;	// TODO: Lookup default group
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
		$val = '';
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
		$val = '';
?>
					<input type="text" name="login" value="<?php echo $val ?>" size="20" maxlength="20" />
				</td>
			</tr>
			<tr>
				<td>Password</td>
				<td>
<?php
	if (isset($_REQUEST['pwnew']) && !empty($_REQUEST['pwnew']))
		$val = stripslashes($_REQUEST['pwnew']);
	else
		$val = '';
?>
					<input type="password" name="pwnew" value="<?php echo $val ?>" size="20" maxlength="20" />
				</td>
			</tr>
			<tr>
				<td>(again)</td>
				<td>
<?php
	if (isset($_REQUEST['pwagain']) && !empty($_REQUEST['pwagain']))
		$val = stripslashes($_REQUEST['pwagain']);
	else
		$val = '';
?>
					<input type="password" name="pwagain" value="<?php echo $val ?>" size="20" maxlength="20" />
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
