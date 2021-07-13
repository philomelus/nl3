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

	define('PAGE', SAP_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

//-----------------------------------------------------------------------------

	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['id']))
	{
		echo invalid_parameters('Edit Period', 'Administration/Periods/Edit.php');
		return;
	}

//-----------------------------------------------------------------------------

	populate_periods();

//-----------------------------------------------------------------------------

	$IID = intval($_REQUEST['id']);

	// If they already modified it and accepted it, update database
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Save Changes')
	{
		// Build update query
		$query = "UPDATE `periods` SET";
		$comma = '';

		// Add title
		if (stripslashes($_REQUEST['title']) != $Periods[$IID]['title'])
		{
			$query .= $comma . " `title` = '" . stripslashes($_REQUEST['title']) . "'";
			$comma = ',';
		}

		do
		{
			// Add changes start date
			$date = valid_date('changesstart', 'Changes Start');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period update failed!</span>';
				break;
			}
			if (strtotime($date) != $Periods[$IID][P_START])
			{
				$query .= $comma . " `changes_start` = '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
				$comma = ',';
			}

			// Add changes end date
			$date = valid_date('changesend', 'Changes End');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period update failed!</span>';
				break;
			}
			if (strtotime($date) != $Periods[$IID][P_END])
			{
				$query .= $comma . " `changes_end` = '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
				$comma = ',';
			}

			// Add bill date
			$bill = valid_date('bill', 'Billing Date');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period update failed!</span>';
				break;
			}
			if (strtotime($bill) != $Periods[$IID][P_BILL])
			{
				$query .= $comma . " `bill` = '" . strftime('%Y-%m-%d', strtotime($bill)) . "'";
				$comma = ',';
			}

			// Add display start date
			$date = valid_date('displaystart', 'Display Start');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period update failed!</span>';
				break;
			}
			if (strtotime($date) != $Periods[$IID][P_DSTART])
			{
				$query .= $comma . " `display_start` = '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
				$comma = ',';
			}

			// Add display end date
			$date = valid_date('displayend', 'Display End');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period update failed!</span>';
				break;
			}
			if (strtotime($date) != $Periods[$IID][P_DEND])
			{
				$query .= $comma . " `display_end` = '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
				$comma = ',';
			}

			// Add due date
			$date = valid_date('due', 'Due Date');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period update failed!</span>';
				break;
			}
			if (strtotime($date) != $Periods[$IID][P_DUE])
			{
				$query .= $comma . " `due` = '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
				$comma = ',';
			}

			// Update the period if needed
			if ($comma == ',')
			{
				$query .= " WHERE `id` = " . $IID . " LIMIT 1";
				$result = db_query($query);
				if (!$result)
				{
					$message = '<span>Period addition failed!</span>';
					break;
				}
			}
			$message = '<span>Period updated successfully!</span>';
		} while (false);
	}

//=============================================================================

//=============================================================================
	$script =
'
<script type="text/javascript" src="../../js/calendar.js"></script>
';

//=============================================================================

	$style = '';

//=============================================================================

	echo gen_htmlHeader('Edit Period', $style, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

	// Add message if available
	if (isset($message) && !empty($message))
	{
?>
		<div><?php echo $message ?></div>
<?php
	}
?>
<!-- EDIT FORM BEGIN -->
	<script type="text/javascript">pathToImages='../../img/';</script>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Title</td>
				<td>
					<input type="text" name="title" value="<?php echo $Periods[$IID]['title'] ?>" size="30" maxlength="30" />
				</td>
			</tr>
			<tr>
				<td>Changes Start</td>
				<td>
<?php
	if (!isset($_REQUEST['changesstartm']))
	{
		$temp = $Periods[$IID][P_START];
		$_REQUEST['changesstartm'] = date('m', $temp);
		$_REQUEST['changesstartd'] = date('d', $temp);
		$_REQUEST['changesstarty'] = date('Y', $temp);
	}
	echo gen_dateField('changesstart', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Changes End</td>
				<td>
<?php
	if (!isset($_REQUEST['changesendm']))
	{
		$temp = $Periods[$IID][P_END];
		$_REQUEST['changesendm'] = date('m', $temp);
		$_REQUEST['changesendd'] = date('d', $temp);
		$_REQUEST['changesendy'] = date('Y', $temp);
	}
	echo gen_dateField('changesend', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Bill</td>
				<td>
<?php
	if (!isset($_REQUEST['billm']))
	{
		$temp = $Periods[$IID][P_BILL];
		$_REQUEST['billm'] = date('m', $temp);
		$_REQUEST['billd'] = date('d', $temp);
		$_REQUEST['billy'] = date('Y', $temp);
	}
	echo gen_dateField('bill', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Display Start</td>
				<td>
<?php
	if (!isset($_REQUEST['displaystartm']))
	{
		$temp = $Periods[$IID][P_DSTART];
		$_REQUEST['displaystartm'] = date('m', $temp);
		$_REQUEST['displaystartd'] = date('d', $temp);
		$_REQUEST['displaystarty'] = date('Y', $temp);
	}
	echo gen_dateField('displaystart', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Display End</td>
				<td>
<?php
	if (!isset($_REQUEST['displayendm']))
	{
		$temp = $Periods[$IID][P_DEND];
		$_REQUEST['displayendm'] = date('m', $temp);
		$_REQUEST['displayendd'] = date('d', $temp);
		$_REQUEST['displayendy'] = date('Y', $temp);
	}
	echo gen_dateField('displayend', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Due Date</td>
				<td>
<?php
	if (!isset($_REQUEST['duem']))
	{
		$temp = $Periods[$IID][P_DUE];
		$_REQUEST['duem'] = date('m', $temp);
		$_REQUEST['dued'] = date('d', $temp);
		$_REQUEST['duey'] = date('Y', $temp);
	}
	echo gen_dateField('due', '', true);
?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Save Changes" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
	</form>
<!-- EDIT FORM END -->
<?php
	echo gen_htmlFooter();
?>
