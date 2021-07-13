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

	define('PAGE', SC_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/popups/customers/adjustments.inc.php';
	require_once 'inc/popups/customers/bills.inc.php';
	require_once 'inc/popups/customers/complaints.inc.php';
	require_once 'inc/popups/customers/payments.inc.php';
	require_once 'inc/popups/customers/service.inc.php';
	require_once 'inc/popups/customers/servicetypes.inc.php';
	require_once 'inc/menubase.inc.php';

	if (!isset($_REQUEST['cid']) || !preg_match('/^0*[1-9]{1}[[:digit:]]*$/', $_REQUEST['cid']))
	{
		echo invalid_parameters('Edit Customer', 'Customers/Edit.php');
		return;
	}
	$CID = intval($_REQUEST['cid']);

	// Generate an update, delete, or insert query as needed to update
	// a name record.  Can also return empty string if nothing needs
	// to be changed.
	function update_name_query($customer_id, $sequence, $first, $last, &$audit = NULL)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;

		$WHERE = "WHERE `customer_id` = " . $customer_id
				. " AND `sequence` = " . $sequence;
		$SET = "SET `first` = '" . db_escape($first) . "',"
				. " `last` = '" . db_escape($last) . "'";
		$name = lup_c_name($customer_id, $sequence);
		if ($err == ERR_NOTFOUND)
			$err = ERR_SUCCESS;
		if (!empty($first) || !empty($last))
		{
			if (!$name || $first != $name->first || $last != $name->last)
			{
				if ($name)
				{
					if (!is_null($audit))
					{
						$audit[nameseq_name($sequence) . ' First Name'] = array($name->first, $first);
						$audit[nameseq_name($sequence) . ' Last Name'] = array($name->last, $last);
					}
					return "UPDATE `customers_names` " . $SET . " " . $WHERE . " LIMIT 1";
				}
				else
				{
					if (!is_null($audit))
					{
						$audit[nameseq_name($sequence) . ' First Name'] = array('', $first);
						$audit[nameseq_name($sequence) . ' Last Name'] = array('', $last);
					}
					return "INSERT INTO `customers_names` " . $SET
							. ", `customer_id` = " . $customer_id
							. ", `sequence` = " . $sequence . " ";

				}
			}
		}
		else if ($name)
		{
			if (!is_null($audit))
			{
				$audit[nameseq_name($sequence) . ' First Name'] = array($name->first, '');
				$audit[nameseq_name($sequence) . ' Last Name'] = array($name->last, '');
			}
			return "DELETE FROM `customers_names` " . $WHERE . " LIMIT 1";
		}
		return '';
	}

	define('IDM_ADDRESSES', 1);
	define('IDM_ADJUSTMENTS', IDM_ADDRESSES + 1);
	define('IDM_BILLS', IDM_ADJUSTMENTS + 1);
	define('IDM_STOPSSTARTS', IDM_BILLS + 1);
	define('IDM_COMPLAINTS', IDM_STOPSSTARTS + 1);
	define('IDM_TYPES', IDM_COMPLAINTS + 1);
	define('IDM_NOTES', IDM_TYPES + 1);
	define('IDM_PAYMENTS', IDM_NOTES + 1);
	define('IDM_SUMMARY', IDM_PAYMENTS  +1);
	define('IDM_MAP', IDM_SUMMARY + 1);
	define('IDM_BILLLOG', IDM_MAP + 1);
	define('IDM_BILLING', IDM_BILLLOG + 1);

	$SUBMENUS = array
		(
			IDM_SUMMARY => array
				(
					MI_PAGE => SCE_SUMMARY,
					MI_NAME => 'Status',
					MI_TIP => 'Status of customer',
					MI_URL => 'edit.php?menu=' . IDM_SUMMARY . '&cid=' . $CID,
					MI_CODE => 'edit/summary.php'
				),
			IDM_NOTES => array
				(
					MI_PAGE => SCE_NOTES,
					MI_NAME => 'Notes',
					MI_TIP => 'Customers notes',
					MI_URL => 'edit.php?menu=' . IDM_NOTES . '&cid=' . $CID,
					MI_CODE => 'edit/notes.php'
				),
			IDM_PAYMENTS => array
				(
					MI_PAGE => SCE_PAYMENTS,
					MI_NAME => 'Payments',
					MI_TIP => 'Customers Payments',
					MI_URL => 'edit.php?menu=' . IDM_PAYMENTS . '&cid=' . $CID,
					MI_CODE => 'edit/payments.php'
				),
			IDM_COMPLAINTS => array
				(
					MI_PAGE => SCE_COMPLAINTS,
					MI_NAME => 'Complaints',
					MI_TIP => 'Customers complaints',
					MI_URL => 'edit.php?menu=' . IDM_COMPLAINTS . '&cid=' . $CID,
					MI_CODE => 'edit/complaints.php'
				),
			IDM_STOPSSTARTS => array
				(
					MI_PAGE => SCE_SERVICE,
					MI_NAME => 'Stops &amp; Starts',
					MI_TIP => 'Customer Stops and Starts',
					MI_URL => 'edit.php?menu=' . IDM_STOPSSTARTS . '&cid=' . $CID,
					MI_CODE => 'edit/stopsstarts.php'
				),
			IDM_TYPES => array
				(
					MI_PAGE => SCE_SERVICETYPES,
					MI_NAME => 'Types',
					MI_TIP => 'Customers Delivery Type changes',
					MI_URL => 'edit.php?menu=' . IDM_TYPES . '&cid=' . $CID,
					MI_CODE => 'edit/types.php'
				),
			IDM_ADJUSTMENTS => array
				(
					MI_PAGE => SCE_ADJUSTMENTS,
					MI_NAME => 'Adjustments',
					MI_TIP => 'Customers adjustments',
					MI_URL => 'edit.php?menu=' . IDM_ADJUSTMENTS . '&cid=' . $CID,
					MI_CODE => 'edit/adjustments.php'
				),
			IDM_BILLS => array
				(
					MI_PAGE => SCE_BILLS,
					MI_NAME => 'Bills',
					MI_TIP => 'Customers bills',
					MI_URL => 'edit.php?menu=' . IDM_BILLS . '&cid=' . $CID,
					MI_CODE => 'edit/bills.php'
				),
			IDM_BILLING => array
				(
					MI_PAGE => SCE_BILLING,
					MI_NAME => 'Billing',
					MI_TIP => 'Customer billing information',
					MI_URL => 'edit.php?menu=' . IDM_BILLING . '&cid=' . $CID,
					MI_CODE => 'edit/billing.php'
				),
			IDM_BILLLOG => array
				(
					MI_PAGE => SCE_BILLLOG,
					MI_NAME => 'Bill Log',
					MI_TIP => 'Display billing log for this customer',
					MI_URL => 'edit.php?menu=' . IDM_BILLLOG . '&cid=' . $CID,
					MI_CODE => 'view/billlog.php'
				),
			IDM_ADDRESSES => array
				(
					MI_PAGE => SCE_ADDRESSES,
					MI_NAME => 'Addresses',
					MI_TIP => 'Delivery and Billing addresses',
					MI_URL => 'edit.php?menu=' . IDM_ADDRESSES . '&cid=' . $CID,
					MI_CODE => 'edit/addresses.php',
					MI_STYLES => array('edit/css/addresses.css')
				),
			IDM_MAP => array
				(
					MI_PAGE => SCE_MAP,
					MI_NAME => 'Map',
					MI_TIP => 'Map of Customers Delivery Address',
					MI_URL => 'edit.php?menu=' . IDM_MAP . '&cid=' . $CID,
					MI_CODE => 'view/map.php'
				),
		);

	// Make sure a menu is selected
	if (!isset($_REQUEST['menu']) || intval($_REQUEST['menu']) == 0)
		$_REQUEST['menu'] = IDM_SUMMARY;
	$MENU = intval($_REQUEST['menu']);

	// Load the code for the menu
	if (file_exists($SUBMENUS[$MENU]['code']))
		include $SUBMENUS[$MENU]['code'];

	//-------------------------------------------------------------------------
	// Get the customer's information for display
	$err = ERR_UNDEFINED;
	$customer = lup_customer($CID);
	if ($err < ERR_SUCCESS)
		return gen_error_page('Customer Edit', $errText, '../../');
	$deliveryAddr = lup_c_address($customer->id, ADDR_C_DELIVERY);
	if ($err < ERR_SUCCESS)
		return gen_error_page('Customer Edit', $errText, '../../');
	$billingAddr = lup_c_address($customer->id, ADDR_C_BILLING);
	if (!$billingAddr)
	{
		$billingAddr = eval("class address { public \$customer_id = 0; public \$address1 = '';"
				. " public \$sequence = " . ADDR_C_BILLING . "; public \$address2 = '';"
				. " public \$city = ''; public \$state = ''; public \$zip = ''; }; return new address();");
	}
	$err = ERR_SUCCESS;	// BUGBUG:  Masks errors from above

	populate_periods();
	if ($err < ERR_SUCCESS)
		return gen_error_page('Customer Edit', $errText, '../../');
	populate_routes();
	if ($err < ERR_SUCCESS)
		return gen_error_page('Customer Edit', $errText, '../../');
	populate_types();
	if ($err < ERR_SUCCESS)
		return gen_error_page('Customer Edit', $errText, '../../');

	//-------------------------------------------------------------------------
	// Save changes if needed
	$errorList = array();
	$message = '';
	$resultHtml = '';
	if ($err >= ERR_SUCCESS && isset($_REQUEST['action']) && function_exists('submit'))
		submit();

	//-------------------------------------------------------------------------
	// Get set up for display of customer info

	$script =
'
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquery.autotab.js"></script>
<script type="text/javascript" src="../../js/calendar.js"></script>
<script type="text/javascript" src="../../js/popups/customers/adjustments.js.php"></script>
<script type="text/javascript" src="../../js/popups/customers/bills.js.php"></script>
<script type="text/javascript" src="../../js/popups/customers/payments.js.php"></script>
';
	if (function_exists('scripts'))
		$script .= scripts();

	$style = '';

//-----------------------------------------------------------------------------

	// Standard headers
	echo gen_htmlHeader('Edit Customer ' . sprintf('C%06d', $CID), $style, $script);

	if ($_REQUEST['menu'] == IDM_MAP)
		echo '<body onload="load()" onunload="GUnload()">';
	else
		echo '<body>';

	// Menu
	gen_menu_secure($SUBMENUS, $_REQUEST['menu'], 1);

	// Message if needed
	if (!empty($message))
		echo '<div>' . $message . '</div><br />';

	// Errors if needed
	if (count($errorList) > 0)
	{
		foreach ($errorList as $error)
			echo $error . '<br />';
	}
	else
	{
		if ($err < ERR_SUCCESS)
			echo gen_error(true, true);
	}

?>
		<script type="text/javascript">pathToImages="../../img/";</script>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<?php
		// Display page
		if (function_exists('display'))
			display();
?>
			<div>
				<input type="hidden" name="cid" value="<?php echo $CID ?>" />
				<input type="hidden" name="menu" value="<?php echo $_REQUEST['menu'] ?>" />
			</div>
		</form>
		<div>
			<?php echo $resultHtml ?>
		</div>
<?php
    echo gen_htmlFooter();
?>
