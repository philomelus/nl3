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

	define('PAGE', ST_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/securitydata.inc.php';
	require_once 'inc/audit.inc.php';
	
//=============================================================================

	// If they are trying to save it, add record to database
	$message = '';
	if (isset($_REQUEST['action']))
	{
		// The other option, 'Refresh', is accomplished simply by being here...
		if ($_REQUEST['action'] == 'Add')
		{
			// Insert new record
			$fields = array
				(
					'page' => intval($_REQUEST['page']),
					'feature' => "'" . $_REQUEST['feature'] . "'",
					'allowed' => "'" . $_REQUEST['allowed'] . "'"
				);
				$group_id = intval($_REQUEST['gid']);
				if ($group_id == 0)
					$fields['group_id'] = 'NULL';
				else
					$fields['group_id'] = $group_id;
				$user_id = intval($_REQUEST['uid']);
				if ($user_id == 0)
					$fields['user_id'] = 'NULL';
				else
					$fields['user_id'] = $user_id;
			$result = db_insert('security', $fields);
			if (!$result)
			{
				$errContext = 'While updating security (' . $_REQUEST['gid'] . ', '
						. $_REQUEST['uid'] . ', ' . $_REQUEST['page'] . ', '
						. $_REQUEST['feature'] . ')';
				$message = '<span>Failled to add security setting!</span>';
			}
			else
			{
				$message = '<span>Security setting added successfully!</span>';
				audit('Added security setting: ' . audit_add($fields));
			}
		}
	}

	$data = SecurityData::create();
	$sdesc = $data->descriptions();
	$spages = $data->pages();

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

	echo gen_htmlHeader('Add Security', $styles, $script);

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
				<td>
					<select name="gid">
						<option value="0">All</option>
<?php
	$query = "SELECT * FROM `groups` ORDER BY `name` ASC";
	$gid = db_query($query);
	if (!$gid)
		return;

	// Generate GID value entries
	while ($val = $gid->fetch_object())
	{
		if (isset($_REQUEST['gid']) && $_REQUEST['gid'] == $val->id)
			$selected = ' selected="selected"';
		else
			$selected = '';
?>
						<option value="<?php echo $val->id ?>"<?php echo $selected ?>><?php echo $val->name ?></option>
<?php
	}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>User</td>
				<td>
					<select name="uid">
						<option value="0">All</option>
<?php
	// Get list of unique users
	$query = "SELECT DISTINCT(`id`) FROM `users` ORDER BY `id` ASC";
	$uid = db_query($query);
	if (!$uid)
		return;

	// Generate UID value entries
	while ($val = $uid->fetch_object())
	{
		$user = lup_user($val->id);
		if ($user)
			$temp = $user->name;
		else
			$temp = sprintf('U%04d', $val->id);
		if (isset($_REQUEST['uid']) && $_REQUEST['uid'] == $val->id)
			$selected = ' selected="selected"';
		else
			$selected = '';
?>
						<option value="<?php echo $val->id ?>"<?php echo $selected ?>><?php echo $temp ?></option>
<?php
	}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Page</td>
				<td>
					<select name='page' onchange="this.form.submit();">
<?php
	// Get list page's and alphabetize them for easy human consumption
	$pages = array_flip($spages);
	ksort($pages);

	// Generate unique page list
	$initial = true;
	foreach($pages as $page => $val)
	{
		if ($initial)
		{
			 if (!isset($_REQUEST['page']) || empty($_REQUEST['page']))
				$_REQUEST['page'] = $val;
			$initial = false;
		}
		if (isset($_REQUEST['page']) && $_REQUEST['page'] == $val)
			$selected = ' selected="selected"';
		else
			$selected = '';
?>
						<option value="<?php echo $val ?>"<?php echo $selected ?>><?php echo $page ?></option>
<?php
	}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Feature</td>
				<td>
<?php
	if (isset($_REQUEST['page']))
		$sfeatures = $data->featuresByPage($_REQUEST['page']);
	else
		$sfeatures = $data->features();
	if (count($sfeatures) == 0)
		$extra = '';
	else
		$extra = '';
?>
					<select name='feature' onchange="this.form.submit();" <?php echo $extra ?>>
<?php
	// Generate unique feature list
	$initial = true;
	foreach ($sfeatures as $feature)
	{
		if ($initial)
		{
			if (!isset($_REQUEST['feature']) || empty($_REQUEST['feature']))
				$_REQUEST['feature'] = $feature;
			$initial = false;
		}
		if (isset($_REQUEST['feature']) && $_REQUEST['feature'] == $feature)
			$selected = ' selected="selected"';
		else
			$selected = '';
		echo '<option' . $selected . '>' . $feature . '</option>';
	}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Allowed</td>
				<td>
<?php
	if (!isset($_REQUEST['allowed']) || empty($_REQUEST['allowed']))
		$_REQUEST['allowed'] = 'Y';
	if (isset($_REQUEST['allowed']) && $_REQUEST['allowed'] == 'Y')
		$checked = ' checked="checked"';
	else
		$checked = '';
?>
					<input type="radio" name="allowed" value="Y"<?php echo $checked?>>Allowed</input>
<?php
	if (isset($_REQUEST['allowed']) && $_REQUEST['allowed'] == 'N')
		$checked = ' checked="checked"';
	else
		$checked = '';
?>
					<input type="radio" name="allowed" value="N"<?php echo $checked?>>Disallowed</input>
				</td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
<?php
		$digits = num_digits($_REQUEST['page']);
		if (($digits % 2) == 1)
			++$digits;
		$page = sprintf('%0' . $digits . 'd', $_REQUEST['page']);
		if (isset($sdesc[$page][$_REQUEST['feature']]))
			$desc = $sdesc[$page][$_REQUEST['feature']];
		else
			$desc = 'Unknown';
		echo $desc;
?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
<?php
	if ($desc == 'Unknown')
		$disabled = ' disabled="disabled"';
	else
		$disabled = '';
?>
					<input type="submit" name="action" value="Add" <?php echo $disabled ?> />
				</td>
			</tr>
		</table>
	</form>
<?php
	echo gen_htmlFooter();
?>
