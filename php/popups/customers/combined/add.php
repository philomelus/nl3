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

	define('PAGE', SCC_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/popups/customers.inc.php';

//=============================================================================

	populate_types();
	populate_periods();

	$id = 0;
	if (isset($_REQUEST['id']))
		$id = intval($_REQUEST['id']);
	unset($combined);
	if ($id > 0)
		$combined = lup_c_combined($id);

//=============================================================================

	// If they already modified it and accepted it, update database
	$message = '';
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Add')
	{
		do
		{
			if (!preg_match('/^[[:digit:]]{1,8}$/', $_REQUEST['customer_id_main']))
			{
				$message = '<span>1Unable to add combined bills due to error.</span>';
				break;
			}
			$id = intval($_REQUEST['customer_id_main']);
			if ($id == 0)
			{
				$message = '<span>2Unable to add combined bills due to error.</span>';
				break;
			}

			if (!preg_match('/^[[:digit:]]{1,6}$/', $_REQUEST['customer_id_secondary']))
			{
				$message = '<span>3Unable to add combined bills due to error.</span>';
				break;
			}
			$id2 = intval($_REQUEST['customer_id_secondary']);
			if ($id2 == 0)
			{
				$message = '<span>4Unable to add combined bills due to error.</span>';
				break;
			}

			if ($id == $id2)
			{
				$message = '<span>5Unable to add combined bills due to error.</span>';
				break;
			}

			// Add the new combined link
			$fields = array
				(
					'customer_id_main' => $id,
					'customer_id_secondary' => $id2,
					'created' => 'NOW()'
				);
			$result = db_insert('customers_combined_bills', $fields);
			if ($result)
			{
				audit('Added combined bills. ' . audit_add($fields));
				$message = '<span>Bill for ' . gen_customerid($id)
						. ' now includes bill for ' . gen_customerid($id2) . '.</span>';
			}
			else
				$message = '<span>Failed to link the combined bills!</span>';
		} while (false);
	}

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/printf.js"></script>
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
<script type="text/javascript" language="JavaScript">
	function ViewCustomer1()
	{
		var c = document.getElementById("customer_id_main");
		if (c)
		{
			CustomerViewPopup(\'cid=\' + c.value, \'../../../\');
		}
	}
	function ViewCustomer2()
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

	echo gen_htmlHeader('Add Combined Customers', $styles, $script);

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
/*
	echo CustomerViewLink($id, '../../');
	printf('%06d', $id);
	echo '</a>';
*/
	if (isset($_REQUEST['customer_id_main']) && intval($_REQUEST['customer_id_main']) > 0)
		$val = intval($_REQUEST['customer_id_main']);
	else if ($id > 0)
		$val = $id;
	else
		$val = 0;
?>
					<input type="text" name="customer_id_main" value="<?php echo $val ?>" size="6" />
					<input type="submit" value="View" onclick="JavaScript:ViewCustomer1(); return false;" />
				</td>
			</tr>
			<tr>
				<td>Combined ID</td>
				<td>
<?php
	if (isset($_REQUEST['customer_id_secondary']) && intval($_REQUEST['customer_id_secondary']) > 0)
		$val = intval($_REQUEST['customer_id_secondary']);
	else
		$val = 0;
?>
					<input type="text" name="customer_id_secondary" value="<?php echo $val ?>" size="6" />
					<input type="submit" value="View" onclick="JavaScript:ViewCustomer2(); return false;" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Add" />
				</td>
			</tr>
		</table>
	</form>
<?php
	echo gen_htmlFooter();
?>
