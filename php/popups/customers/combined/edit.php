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
    define('ROOT', '../../../');
	set_include_path(ROOT . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SCC_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/popups/customers.inc.php';

	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['id']))
	{
		echo invalid_parameters('Edit Combined Customer', 'popups/combined/edit.php');
		return;
	}

	populate_types();
	populate_periods();

	$id = intval($_REQUEST['id']);
	$id2 = 0;
	if (isset($_REQUEST['id2']))
		$id2 = intval($_REQUEST['id2']);
	$combined = lup_c_combined($id, $id2);

	// If they already modified it and accepted it, update database
	$message = '';
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Update')
	{
		$fields = array();
		if (intval($_REQUEST['customer_id_secondary']) != $combined->customer_id_secondary)
			$fields['customer_id_secondary'] = intval($_REQUEST['customer_id_secondary']);

		// Update if needed
		if (count($fields) > 0)
		{
			$result = db_update('customers_combined_bills',
					array
						(
							'customer_id_main' => $combined->customer_id_main,
							'customer_id_secondary' => $combined->customer_id_secondary
						), $fields);
			if ($result)
			{
				audit('Updated combined bill. Main customer is ' . sprintf('%06d', $combined->customer_id_main)
						. '. Secondary customer was ' . sprintf('%06d', $combined->customer_id_secondary)
						. ', now is ' . sprintf('%06d', $fields['customer_id_secondary']) . '.');
				$message = '<span>Changes saved successfully</span>';
				$id2 = intval($_REQUEST['customer_id_secondary']);
				$combined = lup_c_combined($id, $id2);
			}
			else
				$message = '<span>Changes not saved!</span>';
		}
	}

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/printf.js"></script>
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
<script type="text/javascript" language="JavaScript">
	function ViewCustomer()
	{
		var c = document.getElementById("customer_id_secondary");
		if (c)
		{
			CustomerViewPopup(\'cid=\' + c.value, \'../../../\');
		}
	}
</script>
';

	//-------------------------------------------------------------------------

	echo gen_htmlHeader('Edit Combined Customers', $styles, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

	// Show message if needed
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
				<td>Main ID</td>
				<td>
<?php
	echo CustomerViewLink($id, '../../../');
	printf('%06d', $id);
	echo '</a>';
?>
				</td>
			</tr>
			<tr>
				<td>Combined ID</td>
				<td>
<?php
	if (isset($_REQUEST['customer_id_secondary']) && intval($_REQUEST['customer_id_secondary']) > 0)
		$val = intval($_REQUEST['customer_id_secondary']);
	else
		$val = $id2;
?>
					<input type="text" name="customer_id_secondary" value="<?php echo $val ?>" size="6" />
					<input type="submit" value="View" onclick="JavaScript:ViewCustomer(); return false;" />
				</td>
			</tr>
			<tr>
				<td>Created</td>
				<td>
<?php
	echo strftime('%m-%d-%Y %H:%M:%S', strtotime($combined->created));
?>
				</td>
			</tr>
			<tr>
				<td>Last Updated</td>
				<td>
<?php
	echo strftime('%m-%d-%Y %H:%M:%S', strtotime($combined->updated));
?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Update" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $id ?>" />
		<input type="hidden" name="id2" value="<?php echo $id2 ?>" />
	</form>
<?php
	echo gen_htmlFooter();
?>
