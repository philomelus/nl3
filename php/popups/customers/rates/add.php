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
	define('CONTEXT', 'Customers Administration Rates Add');

	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SCDR_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

	populate_types();
	populate_periods();

	$CBT = get_config('customer-billing-type');
	if ($CBT == CFG_NONE)
		$CBT = 'auto';
	
//=============================================================================

	// If they already modified it and accepted it, update database
	$message = '';
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Add')
	{
		// Build field lists
		$type = intval($_REQUEST['tid']);
		$begin = intval($_REQUEST['begin']);
		$end = intval($_REQUEST['end']);
		$rate = doubleval($_REQUEST['rate']);
		if ($CBT == 'auto')
		{
			$daily = 0;
			$sunday = 0;
		}
		else
		{
			$daily = doubleval($_REQUEST['daily']);
			$sunday = doubleval($_REQUEST['sunday']);
		}
		
		do
		{
			// Make sure end period is after beginning period
			if ($end > 0 && $begin > $end)
			{
				$err = ERR_FAILURE;
				$errCode = ERR_FAILURE;
				$errContext = CONTEXT;
				$errQuery = '';
				$errText = 'Beginning period must be before ending period!';
				break;
			}

			// If ending at current, make sure one doesn't already exist
			if ($end == 0)
			{
				$temp = db_query_result('SELECT COUNT(*) FROM `customers_rates` WHERE `type_id` = '
						. $type . ' AND `period_id_end` <=> NULL');
				if (!$temp && $err < ERR_SUCCESS)
					break;
				if ($temp > 0)
				{
					$err = ERR_FAILURE;
					$errCode = ERR_FAILURE;
					$errContext = CONTEXT;
					$errQuery = '';
					$errText = 'Current rate for type already exists!';
					break;
				}
			}

			// Make sure periods don't coinside with other rates for the periods
			$query = 'SELECT COUNT(*) FROM `customers_rates` WHERE `type_id` = ' . $type;
			if ($end == 0)
			{
				$query .= ' AND (`period_id_begin` >= ' . $begin
						. ' OR `period_id_end` >= ' . $begin . ' OR `period_id_end` <=> NULL)';
			}
			else
			{
				$query .= ' AND (`period_id_begin` BETWEEN ' . $begin . ' AND ' . $end
					. ' OR `period_id_end` BETWEEN ' . $begin . ' AND ' . $end . ')';
			}
			$temp = db_query_result($query);
			if (!$temp && $err < ERR_SUCCESS)
				break;
			if ($temp > 0)
			{
				$err = ERR_FAILURE;
				$errCode = ERR_FAILURE;
				$errContext = CONTEXT;
				$errQuery = '';
				$errText = 'Rate for type and periods already exists!';
				break;
			}

			// If there is a current period for the type, make sure the
			// periods don't exist in the future
			$temp = db_query_result('SELECT COUNT(*) FROM `customers_rates` WHERE `type_id` = '
					. $type . ' AND `period_id_end` <=> NULL');
			if (!$temp && $err < ERR_SUCCESS)
				break;
			if ($temp > 0)
			{
				$billPeriod = get_config('billing-period');
				if ($begin >= $billPeriod || ($end > 0 && $end >= $billPeriod))
				{
					$err = ERR_FAILURE;
					$errCode = ERR_FAILURE;
					$errContext = CONTEXT;
					$errQuery = '';
					$errText = 'Current rate for type covers at least part of those periods!';
					break;
				}
			}

			// Add the new rate
			$fields = array
				(
					'type_id' => $type,
					'period_id_begin' => $begin,
					'period_id_end' => $end,
					'created' => 'NOW()',
					'rate' => $rate,
					'daily_credit' => $daily,
					'sunday_credit' => $sunday
				);
            if ($end == 0)
                $fields['period_id_end'] = 'NULL';
			$result = db_insert('customers_rates', $fields);
			if (!$result)
				$message = '<span>Rate addition failed!</span>';
			else
			{
				$end = $fields['period_id_end'];
				audit('Added new rate. '
					. 'type_id = ' . tid2abbr($fields['type_id'], true) . '. '
					. 'period_id_begin = ' . iid2title($fields['period_id_begin'], true) . '. '
					. 'period_id_end = ' . ($end == 0 ? 'Current' : iid2title($fields['period_id_end'], true)) . '. '
					. 'rate = ' . sprintf('%01.2f', $fields['rate']) . '. '
					. 'daily credit = ' . sprintf('%01.4f', $fields['daily_credit']) . '. '
					. 'sunday credit = ' . sprintf('%01.4f', $fields['sunday_credit']) . '.');

				$message = '<span>Rate added successfully!</span>';
			}
		} while (false);
		if (empty($message))
			$message = '<span>Rate addition failed!</span>';
	}

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------
	$script =
'
';

	//-------------------------------------------------------------------------

	echo gen_htmlHeader('Add Rate', $styles, $script);

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
				<td>Type</td>
				<td>
<?php
	if (isset($_REQUEST['tid']) && is_numeric($_REQUEST['tid']))
		$val = intval($_REQUEST['tid']);
	else
		$val = 0;
	echo gen_typeSelect('tid', $val, false, '', array(), '', false);
?>
				</td>
			</tr>
			<tr>
				<td>Begin</td>
				<td>
<?php
	if (isset($_REQUEST['begin']) && is_numeric($_REQUEST['begin']))
		$val = intval($_REQUEST['begin']);
	else
		$val = 0;
	echo gen_periodsSelect('begin', $val, false, '', '');
?>
				</td>
			</tr>
			<tr>
				<td>End</td>
				<td>
<?php
	if (isset($_REQUEST['end']) && is_numeric($_REQUEST['end']))
		$val = intval($_REQUEST['end']);
	else
		$val = 0;
	echo gen_periodsSelect('end', $val, false, '', '', array(0 => 'Current'));
?>
				</td>
			</tr>
			<tr>
				<td>Rate</td>
				<td>
<?php
	if (isset($_REQUEST['rate']) && is_numeric($_REQUEST['rate']))
		$val = sprintf('%01.2f', $_REQUEST['rate']);
	else
		$val = '0.00';
?>
					$<input type="text" name="rate" value="<?php echo $val ?>" size="8" />
				</td>
			</tr>
<?php
	if ($CBT != 'auto')
	{
		echo '<tr>'
				. '<td>Daily Credit</td>'
				. '<td>';
		if (isset($_REQUEST['daily']) && is_numeric($_REQUEST['daily']))
			$val = sprintf('%01.4f', $_REQUEST['daily']);
		else
			$val = '0.0000';
		echo '$<input type="text" name="daily" value="' . $val . '" size="8" />'
				. '</td>'
				. '</tr>'
				. '<tr>'
				. '<td>Sunday Credit</td>'
				. '<td>';
		if (isset($_REQUEST['sunday']) && is_numeric($_REQUEST['sunday']))
			$val = sprintf('%01.4f', $_REQUEST['sunday']);
		else
			$val = '0.0000';
		echo '$<input type="text" name="sunday" value="' . $val . '" size="8" />'
				. '</td>'
				. '</tr>';
	}
?>
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
