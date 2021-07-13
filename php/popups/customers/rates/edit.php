<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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
	define('CONTEXT', 'Customers/Administration/Rates/Edit');

	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SCDR_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

//=============================================================================

	// Make sure we were passed configuration keys.  Use REQUEST since it can
    // be on initial open via GET or after sumbmission via POST.
	if (!isset($_REQUEST['id']) || !isset($_REQUEST['s']) || !isset($_REQUEST['e']))
	{
		echo invalid_parameters('Edit Customer Rate', 'popups/customers/rates/edit.php');
		return;
	}

//=============================================================================

	populate_types();
	populate_periods();

	$CBT = get_config('customer-billing-type', 'auto');
	
	$rate = lup_c_rate($_REQUEST['id'], $_REQUEST['s'], $_REQUEST['e']);
    if (!$rate)
        return gen_error_page('Edit Customer Rate', $errText, '../../..');

//=============================================================================

	// If they already modified it and accepted it, update database
	$message = '';
	if (isset($_POST['action']) && $_POST['action'] == 'Save Changes')
	{
		$type = $rate->type_id;	// For logic
		$begin = intval($_POST['beginNew']);
		$end = intval($_POST['endNew']);
		$newRate = floatval($_POST['rate']);
		if ($CBT == 'auto')
		{
			$daily = 0;
			$sunday = 0;
		}
		else
		{
			$daily = floatval($_POST['daily']);
			$sunday = floatval($_POST['sunday']);
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

            // BUGBUG:
			// - If ending at current, make sure one doesn't already exist
			// - Make sure periods don't coinside with other rates for the periods
			// - If there is a current period for the type, make sure the
			//   periods don't exist in the future

			// Build query
			$query = "UPDATE `customers_rates` SET";
			$comma = '';
			$audit = array();
			if ($begin != $rate->period_id_begin)
			{
				$query .= $comma . " `period_id_begin` = " . $begin;
				$comma = ',';
				$audit['period_id_begin'] = array(iid2title($rate->period_id_begin, true),
						($begin == 0 ? 'Current' : iid2title($begin, true)));
			}
			if ($end != $rate->period_id_end)
			{
                if ($end == 0)
                    $query .= $comma . " `period_id_end` = NULL";
                else
    				$query .= $comma . " `period_id_end` = " . $end;
				$comma = ',';
				$audit['period_id_end'] = array((is_null($rate->period_id_end) ? 'Current' : iid2title($rate->period_id_end, true)),
						($end == 0 ? 'Current' : iid2title($end, true)));
			}
			if ($newRate != $rate->rate)
			{
				$query .= $comma . " `rate` = " . $newRate;
				$comma = ',';
				$audit['rate'] = array(sprintf('$%01.2f', $rate->rate), sprintf('$%01.2f', $newRate));
			}
			if ($daily != $rate->daily_credit)
			{
				$query .= $comma . " `daily_credit` = " . $daily;
				$comma = ',';
				$audit['daily_credit'] = array(sprintf('$%01.4f', $rate->daily_credit), sprintf('$%01.4f', $daily));
			}
			if ($sunday != $rate->sunday_credit)
			{
				$query .= $comma . " `sunday_credit` = " . $sunday;
				$comma = ',';
				$audit['sunday_credit'] = array(sprintf('$%01.4f', $rate->sunday_credit), sprintf('$%01.4f', $sunday));
			}

			// Update rate if needed
			if ($comma == ',')
			{
				$query .= " WHERE `type_id` = " . $_REQUEST['id'] . " AND `period_id_begin` = " . $_REQUEST['s']
						. " AND `period_id_end` <=> " . (intval($_REQUEST['e']) == 0 ? 'NULL' : $_REQUEST['e'])
                        . " LIMIT 1";
				$result = db_query($query);
				if (!$result)
					$message = '<span>Rate update failed!</span>';
				else
				{
					audit('Updated rate for type ' . tid2abbr($rate->type_id, true) . '. ' . audit_update($audit));
					$message = '<span>Rate updated successfully!</span>';
					$_REQUEST['s'] = $begin;
					$_REQUEST['e'] = $end;
					$rate = lup_c_rate($_REQUEST['id'], $_REQUEST['s'], $_REQUEST['e']);
                    if (!$rate)
                        return gen_error_page('Edit Customer Rate', $errText, '../../..');
				}
			}
			else
				$message = '<span>No changes to save.</span>';
		} while (false);
	}

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------
	$script =
'
';

	//-------------------------------------------------------------------------

	echo gen_htmlHeader('Edit Rate', $styles, $script);

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
	echo $DeliveryTypes[$rate->type_id]['abbr'];
?>
				</td>
			</tr>
			<tr>
				<td>Begin</td>
				<td>
<?php
    if (isset($_POST['beginNew']))
        $temp = intval($_POST['beginNew']);
    else
        $temp = $rate->period_id_begin;
	echo gen_periodsSelect('beginNew', $temp, false, '', '');
?>
				</td>
			</tr>
			<tr>
				<td>End</td>
				<td>
<?php
    if (isset($_POST['endNew']))
        $temp = intval($_POST['endNew']);
    else
    {
        if (is_null($rate->period_id_end))
            $temp = 0;
        else
            $temp = $rate->period_id_end;
    }
	echo gen_periodsSelect('endNew', $temp, false, '', '', array(0 => 'Current'));
?>
				</td>
			</tr>
			<tr>
				<td>Rate</td>
				<td>
<?php
    if (isset($_POST['rate']))
        $temp = floatval($_POST['rate']);
    else
        $temp = $rate->rate;
	$val = sprintf('%01.2f', $temp);
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
		if (isset($_POST['daily']))
			$val = sprintf('%01.4f', floatval($_POST['daily']));
		else
			$val = sprintf('%01.4f', $rate->daily_credit);
		echo '$<input type="text" name="daily" value="' . $val . '" size="8" />'
				. '</td>'
				. '</tr>'
				. '<tr>'
				. '<td>Sunday Credit</td>'
				. '<td>';
		if (isset($_POST['sunday']))
			$val = sprintf('%01.4f', floatval($_POST['sunday']));
		else
			$val = sprintf('%01.4f', $rate->sunday_credit);
		echo '$<input type="text" name="sunday" value="' . $val . '" size="8" />'
				. '</td>'
				. '</tr>';
	}
?>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Save Changes" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
		<input type="hidden" name="s" value="<?php echo $_REQUEST['s'] ?>" />
		<input type="hidden" name="e" value="<?php echo $_REQUEST['e'] ?>" />
	</form>
<?php
	echo gen_htmlFooter();
?>
