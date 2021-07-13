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

	require_once 'tools/bill.php';
	
	$message = '';

	function subsubdisplay()
	{
        global $smarty;
		global $resultHtml, $message, $Period, $err, $DB, $errCode, $errContext, $errQuery, $errText;

		// Get current billing status
		$state = intval(get_globalConfig('billing-status', BILL_COMPLETE));
		
        $ranges = array();
		try
		{
			$min = $max = 0;
			
			// Get the range of customer id's
			$query = 'SELECT MIN(`id`), MAX(`id`) FROM `customers`';
			$temp = $DB->query($query);
			if (!$temp)
			{
				$err = ERR_DB_QUERY;
				$errContext = 'Customer Billing Step 3';
				$errQuery = $query;
				throw new Exception($DB->error, $DB->errno);
			}
			$row = $temp->fetch_row();
			$temp->close();
			$min = intval($row[0]);
			$max = intval($row[1]);

			// Determine ranges, and whether they've been billed already
			$start = $min;
			$done = 0;
			$count = 0;
			while ($start < $max)
			{
				$begin = $start;
				$end = ($start + 249 > $max ? $max : $start + 249);
				
				if ($state == BILL_RUNNING || $state == BILL_PENDING)
				{
					$query = 'SELECT COUNT(*) FROM `customers_bills` WHERE `cid` BETWEEN \''
							. sprintf('%06d', $begin) . '\' AND \'' . sprintf('%06d', $end)
							. '\' AND `iid` = ' . $Period[P_PERIOD];
					$temp = $DB->query($query);
					if (!$temp)
					{
						$err = ERR_DB_QUERY;
                        $errContext = 'Customer Billing Step 3';
						$errQuery = $query;
						throw new Exception($DB->error, $DB->errno);
					}
					$row = $temp->fetch_row();
					$temp->close();
					$status = (intval($row[0]) > 0 ? true : false);
					if ($status)
						++$done;
					++$count;
				}
				else
					$status = true;
				$ranges[] = array($begin, sprintf('%d - %d', $begin, $end), $status);
				$start += 250;
			}
			
			// If the billing is done, then update status
			if ($done == $count && $state == BILL_RUNNING)
			{
				set_globalConfig('billing-status', BILL_GENERATED);
                $oldState = $state;
				$state = BILL_GENERATED;
                audit('Updated billing state from ' . $oldState
                    . ' to ' . $state . '.');
			}
		}
		catch (Exception $e)
		{
			$errCode = $e->getCode();
            $errContext = 'Customer Billing Step 3';
			$errText = $e->getMessage();
			log_error();
		}
	    $smarty->assign('ranges', $ranges);
        
		switch ($state)
		{
		case BILL_PENDING:		$dstate = 'Ready';						break;
		case BILL_RUNNING:		$dstate = 'In Process';					break;
		case BILL_GENERATED:	$dstate = 'Repairing';					break;
		case BILL_COMBINED:		$dstate = 'Combined';					break;
		default:
		case BILL_COMPLETE:		$dstate = 'Complete';					break;
		}
        $smarty->assign('dstate', $dstate);
        $smarty->assign('period', $Period[P_PERIOD]);
        $smarty->assign('action', $_SERVER['PHP_SELF'] . '?menu=4&amp;submenu=1&amp;m3=3');

		$smarty->assign('menu', $_REQUEST['menu']);
		$smarty->assign('submenu', $_REQUEST['submenu']);
		$smarty->assign('m3', $_REQUEST['m3']);

        $smarty->display('customers/billing/bill/step3.tpl');
	}

	function subsubsubmit()
	{
		global $err, $Period, $message;

		// Make sure status has been updated
		$state = intval(get_globalConfig('billing-status', BILL_COMPLETE));
		if ($state == BILL_PENDING)
			set_globalConfig('billing-status', BILL_RUNNING);
		
		// Determine which range of customers to bill
		$start = intval($_POST['action']);
		
		// Get list of customer id's
		$end = $start + 249;
		$query =
'
SELECT
	`id`
FROM
	`customers`
WHERE
	`active` = \'Y\'
	AND `started` <= \'' . strftime('%Y-%m-%d', $Period[P_END]) . '\'
	AND `id` BETWEEN ' . $start . ' AND ' . $end . '
ORDER BY
	`id`';
		$records = db_query($query);
		if (!$records)
		{
			log_error();
			return;
		}

		$BILL = new Biller();
		while ($record = $records->fetch_object())
		{
			$BILL->Generate($record->id, $Period[P_PERIOD]);
		}
		
		$message = '<span>Billed customers ' . $start . ' - ' . $end . ' successfully.</span>';
	}
?>
