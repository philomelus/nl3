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

	define('PAGE', SAP_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

//=============================================================================

	// If they already modified it and accepted it, update database
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Add')
	{
		// Build field array
		$fields = array();
		$message = '';

		do
		{
			// Add title
			$fields['title'] = "'" . stripslashes($_REQUEST['title']) . "'";

			// Validate changes start date
			$changesStart = valid_date('changesstart', 'Changes Start');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period addition failed!</span>';
				break;
			}

			// Validate changes end date
			$changesEnd = valid_date('changesend', 'Changes End');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period addition failed!</span>';
				break;
			}

			// Validate bill date
			$bill = valid_date('bill', 'Billing Date');
			if ($err < ERR_SUCCESS)
			{
				$message = '<spac>Period addition failed!</span>';
				break;
			}

			// Validate display start date
			$displayStart = valid_date('displaystart', 'Display Start');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period addition failed!</span>';
				break;
			}

			// Validate display end date
			$displayEnd = valid_date('displayend', 'Display End');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period addition failed!</span>';
				break;
			}

			// Validate due date
			$due = valid_date('due', 'Due date');
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period addition failed!</span>';
				break;
			}

			// Make sure start date isn't after end date
			if (strtotime($changesStart) > strtotime($changesEnd))
			{
				$message = '<span>Changes Start is after Changed End</span>';
				break;
			}
			if (strtotime($displayStart) > strtotime($displayEnd))	// TODO:  doh, strtotime!
			{
				$message = '<span>Display Start is after Display End</span>';
				break;
			}
			
			// Make sure start dates aren't in any current periods
			$changesStartsql = "'" . strftime('%Y-%m-%d', strtotime($changesStart)) . "'";
			$query = 'SELECT `id` FROM `periods` WHERE ' . $changesStartsql . ' BETWEEN `changes_start` AND `changes_end`';
			$badiid = db_query_result($query);
			if (!$badiid && $err < ERR_SUCCESS)
			{
				$message = '<span>Unable to determine whether Changes Start is in use</span>';
				break;
			}
			if ($errCode != ERR_NOTFOUND)
			{
				$badperiod = gen_periodArray($badiid);
				$message = '<span>Changes Start is already used in ' . $badperiod[P_TITLE] . '</span>';
				break;
			}

			// Make sure end date isn't in any current periods
			$changesEndsql = "'" . strftime('%Y-%m-%d', strtotime($changesEnd)) . "'";
			$query = 'SELECT `id` FROM `periods` WHERE ' . $changesEndsql . ' BETWEEN `changes_start` AND `changes_end`';
			$badiid = db_query_result($query);
			if (!$badiid && $err < ERR_SUCCESS)
			{
				$message = '<span>Unable to determine whether Changes End is in use</span>';
				break;
			}
			if ($errCode != ERR_NOTFOUND)
			{
				$badperiod = gen_periodArray($badiid);
				$message = '<span>Changes End is already used in ' . $badperiod[P_TITLE] . '</span>';
				break;
			}

			// Add validated dates to array
			$fields['created'] = 'NOW()';
			$fields['changes_start'] = $changesStartsql;
			$fields['changes_end'] = $changesEndsql;
			$fields['bill'] = "'" . strftime('%Y-%m-%d', strtotime($bill)) . "'";
			$fields['display_start'] = "'" . strftime('%Y-%m-%d', strtotime($displayStart)) . "'";
			$fields['display_end'] = "'" . strftime('%Y-%m-%d', strtotime($displayEnd)) . "'";
			$fields['due'] = "'" . strftime('%Y-%m-%d', strtotime($due)) . "'";

			// Add new period
			$period = db_insert('periods', $fields);
			if ($err < ERR_SUCCESS)
			{
				$message = '<span>Period addition failed!</span>';
				break;
			}
			$message = '<span>Period added successfully as <span>'
					. sprintf('I%04d', $period) . '</span></span>';
		} while (false);
	}

//=============================================================================
	$script =
'
<script type="text/javascript" src="../../js/calendar.js"></script>
';

//=============================================================================

	$style = '';

//=============================================================================

	echo gen_htmlHeader('Add Period', $style, $script);

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
					<input type="text" name="title" value="<?php echo $_REQUEST['title'] ?>" size="30" maxlength="30" />
				</td>
			</tr>
			<tr>
				<td>Changes Start</td>
				<td>
<?php
	echo gen_dateField('changesstart', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Changes End</td>
				<td>
<?php
	echo gen_dateField('changesend', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Bill Date</td>
				<td>
<?php
	echo gen_dateField('bill', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Display Start</td>
				<td>
<?php
	echo gen_dateField('displaystart', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Display End</td>
				<td>
<?php
	echo gen_dateField('displayend', '', true);
?>
				</td>
			</tr>
			<tr>
				<td>Due Date</td>
				<td>
<?php
	echo gen_dateField('due', '', true);
?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Add" />
				</td>
			</tr>
		</table>
	</form>
<!-- EDIT FORM END -->
<?php
	echo gen_htmlFooter();
?>
