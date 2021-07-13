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

	require_once 'tools/bill.php';

	$message = '';

	function subsubdisplay()
    {
        global $smarty;
		global $billResult, $err, $Period;

		$state = intval(get_globalConfig('billing-status', BILL_COMPLETE));
		
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
        $smarty->assign('action', $_SERVER['PHP_SELF'] . '?menu=4&amp;submenu=1&amp;m3=4');

		if ($state == BILL_GENERATED)
			$disable = '';
		else
			$disable = ' disabled="disabled"';
        $smarty->assign('generated', $disable);
        $smarty->assign('billResult', $billResult);
        if (!isset($_REQUEST['customer_id']))
            $_REQUEST['customer_id'] = '';
        $smarty->assign('customer_id', $_REQUEST['customer_id']);

		$smarty->assign('menu', $_REQUEST['menu']);
		$smarty->assign('submenu', $_REQUEST['submenu']);
		$smarty->assign('m3', $_REQUEST['m3']);

        $smarty->display('customers/billing/bill/step4.tpl');
	}

	function subsubsubmit()
	{
		global $Period, $message;

		if ($_POST['action'] == 'Re-Bill')
		{
			$time = time();
			$customer_id = intval($_REQUEST['customer_id']);
			$period_id = get_config('billing-period');

			// Bill
			$BILL = new Biller();
			$BILL->Generate($customer_id, $period_id);
			unset($BILL);

			// Retreive the log for the customer
			$records = db_query('SELECT `sequence`, `what` FROM `customers_bills_log` WHERE `customer_id` = '
					. $customer_id . ' AND `period_id` = ' . $period_id . ' AND `when` >= \''
					. strftime('%Y-%m-%d', $time) . '\' ORDER BY `when` DESC, `sequence` DESC');
			if ($records)
			{
				$result = '<hr />'
						. '<table>'
						. '<thead>'
						. '<tr>'
						. '<th>Line</th>'
						. '<th>Message</th>'
						. '</tr>'
						. '</thead>'
						. '<tbody>';
				if ($records->num_rows > 0)
				{
					while ($record = $records->fetch_object())
					{
						$result .= '<tr>'
								. '<td>' . sprintf('%04d', $record->sequence) . '</td>'
								. '<td>' . htmlspecialchars($record->what) . '</td>'
								. '</tr>';
					}
				}
				else
					$result .= '<tr><td colspan="2">None</td></tr>';
				$result .= '</tbody></table>';
				return $result;
			}
		}
	}

?>
