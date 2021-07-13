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

	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SD_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/errors.inc.php';
	require_once 'inc/database.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/sql.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/popups/customers.inc.php';

	//-------------------------------------------------------------------------
	// Make sure we were passed required parameters
	if (!isset($_REQUEST['aid']))
	{
		echo invalid_parameters('View Adjustment', 'Customers/Adjustments/View.php');
		return;
	}
	$AID = intval($_REQUEST['aid']);

	//-------------------------------------------------------------------------
	// Get adjustment record
	$adjustment = lup_c_adjustment($AID);

//=============================================================================
// MAIN DISPLAY

	//-------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/popups/customers.js.php"></script>
';

	//-------------------------------------------------------------------------
	$style = '';

	$AIDSTR = sprintf('A%08d', $AID);
	echo gen_htmlHeader('View Adjustment ' . $AIDSTR, $style, $script);

?>
	<body>
<?php
	// Display error(s) if needed
	if ($err < ERR_SUCCESS)
		echo gen_error(true, true);

?>
		<table>
			<tr>
				<td>AID</td>
				<td>
					<span><?php echo $AIDSTR ?></span>
				</td>
			</tr>
			<tr>
				<td>CID</td>
				<td>
<?php
	echo CustomerViewLink($adjustment->customer_id, '../../../') . sprintf('%06d', $adjustment->customer_id) . '</a>';
?>
				</td>
			</tr>
			<tr>
				<td>Period</td>
				<td>
<?php
    if (is_null($adjustment->period_id))
        echo '<span>pending</span>';
    else
        echo iid2title($adjustment->period_id);
?>
				</td>
			</tr>
			<tr>
				<td>Description</td>

<?php
	$val = valid_text(htmlspecialchars(stripslashes($adjustment->desc)));
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>Amount</td>
<?php
	$val = currency(floatval($adjustment->amount));
?>
				<td><?php echo $val ?></td>
			</tr>
			<tr>
				<td>Note</td>
<?php
	$val = valid_text(htmlspecialchars(stripslashes($adjustment->note)));
	if ($val == '&nbsp;')
		$val = '<span>blank</span>';
?>
				<td><?php echo $val ?></td>
			</tr>
		</table>
<?php
    echo gen_htmlHeader();
?>
