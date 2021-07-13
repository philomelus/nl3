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

	define('PAGE', SU_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

//-------------------------------------------------------------------------

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Add')
	{
		$fields = array();
		$fields['title'] = "'" . db_escape($_REQUEST['title']) . "'";
		$fields['active'] = '\'' . $_POST['active'] . '\'';
		$result = db_insert('routes', $fields);
		if (!$result)
			$message = '<span>Route addition failed!</span>';
		else
			$message = '<span>Added route ' . sprintf('R%04d', $result) . '!</span>';
	}

//-----------------------------------------------------------------------------
	$styles = '';

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript">
</script>
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Add Route', $styles, $script);

	if (isset($message) && !empty($message))
	{
?>
		<div><?php echo $message ?></div>
<?php
	}
?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Title</td>
				<td>
<?php
	if (isset($_REQUEST['title']) && !empty($_REQUEST['title']))
		$val = stripslashes($_REQUEST['title']);
	else
		$val = '';
?>
					<input type="text" name="title" value="<?php echo $val ?>" size="20" maxlength="20" />
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
		$val['Y'] = ' checked="checked"';
?>
					<input type="radio" name="active" value="Y"<?php echo $val['Y'] ?>>Yes</input>
					<input type="radio" name="active" value="N"<?php echo $val['N'] ?>>No</input>
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
