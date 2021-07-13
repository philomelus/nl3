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

	define('PAGE', ST_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/securitydata.inc.php';

	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['gid']) || !isset($_REQUEST['uid']) || !isset($_REQUEST['page'])
			|| !isset($_REQUEST['feature']))
	{
		echo invalid_parameters('Edit Security', 'Administration/Security/Edit.php');
		return;
	}

	// Get the security record
	$gid = $_REQUEST['gid'];
	$uid = $_REQUEST['uid'];
	$page = $_REQUEST['page'];
	$feature = $_REQUEST['feature'];
	$security = lup_security($page, $feature, $uid, $gid);
	if (!$security)
	{
		fatal_error('Edit Security', 'Unable to locate security.');
		if ($err < ERR_SUCCESS)
			echo gen_error();
		return;
	}
	if ($uid)
	{
		$user = lup_user($uid);
		if (!$user)
		{
			fatal_error('Edit Security', 'Unable to locate user.');
			if ($err < ERR_SUCCESS)
				echo gen_error();
			return;
		}
	}
	if ($gid)
	{
		$group = lup_group($gid);
		if (!$group)
		{
			fatal_error('Edit Security', 'Unable to locate group.');
			if ($err < ERR_SUCCESS)
				echo gen_error();
			return;
		}
	}
	$err = ERR_SUCCESS;

	$data = SecurityData::create();
	$sdesc = $data->descriptions();
	$spages = $data->pages();

//=============================================================================

	// If they already modified and accepted it, update database
	$message = '';
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Save Changes')
	{
		// Determine whether we need to update anything
		$updates = array();
		if ($_POST['allowed'] != $security->allowed)
			$updates['allowed'] = "'" . $_POST['allowed'] . "'";

		// Update database if needed
		if (count($updates) > 0)
		{
			$query = "UPDATE `security` SET";
			$comma = '';
			foreach($updates as $field => $val)
			{
				$query .= $comma . ' `' . $field . '` = ' . $val;
				$comma = ',';
			}
			$query .= ' WHERE `page` = ' . $page . ' AND `feature` = \'' . $feature . '\'';
			if (strlen($gid) == 0)
				$query .= ' AND ISNULL(`group_id`)';
			else
				$query .= ' AND `group_id` = ' . intval($gid);
			if (strlen($uid) == 0)
				$query .= ' AND ISNULL(`user_id`)';
			else
				$query .= ' AND `user_id` = ' . intval($uid);
			$result = db_query($query);
			if (!$result)
				$message = '<span>Changes not saved!</span>';
			else
				$message = '<span>Changes saved successfully!</span>';
			$security = lup_security($page, $feature, $uid, $gid);
		}
	}

//-----------------------------------------------------------------------------
	$styles =
'
';

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript">
</script>
';

//-----------------------------------------------------------------------------

	$temp = num_digits($page);
	if ($temp % 2)
		++$temp;
	$pagestr = sprintf('%0' . $temp . 'd', $page);
	$pagename = $spages[$pagestr];
	if (empty($pagename))
		$pagename = $pagestr;
	if (!isset($group))
		$groupname = 'All';
	else
		$groupname = $group->name;
	if (!isset($user))
		$username = 'All';
	else
		$username = $user->name;
	$title = $groupname . ' - ' . $username . ' - ' . $pagename . ' ( ' . $feature . ' )';
	echo gen_htmlHeader('Edit ' . $title, $styles, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

?>
<!-- EDIT FORM BEGIN -->
<?php
	if (!empty($message))
	{
?>
	<div><?php echo $message ?></div>
<?php
	}
?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Group</td>
				<td><?php echo $groupname ?></td>
			</tr>
			<tr>
				<td>User</td>
				<td><?php echo $username ?></td>
			</tr>
			<tr>
				<td>Page</td>
				<td><?php echo $pagename ?></td>
			</tr>
			<tr>
				<td>Feature</td>
				<td><?php echo $feature ?></td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
<?php
	if (isset($sdesc[$pagestr][$security->feature]))
		$desc = $sdesc[$pagestr][$security->feature];
	else
		$desc = 'Unknown';
	echo $desc;
?>
				</td>
			</tr>
			<tr>
				<td>Allowed</td>
				<td>
					<input type="radio" name="allowed" value="Y"<?php if ($security->allowed == 'Y') echo ' checked="checked"'; ?>>Allowed</input>
					<input type="radio" name="allowed" value="N"<?php if ($security->allowed == 'N') echo ' checked="checked"'; ?>>Disallowed</input>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Save Changes" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="gid" value="<?php echo $gid ?>" />
		<input type="hidden" name="uid" value="<?php echo $uid ?>" />
		<input type="hidden" name="page" value="<?php echo $page ?>" />
		<input type="hidden" name="feature" value="<?php echo $feature ?>" />
	</form>
<!-- EDIT SECURITY FORM END -->
<?php
	echo gen_htmlFooter();
?>
