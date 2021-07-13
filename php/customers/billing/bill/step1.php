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

	$message = '';

	function subsubdisplay()
    {
        global $smarty;
		global $resultHtml, $message;
		global $Period;
		global $err;

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

        $smarty->assign('action', $_SERVER['PHP_SELF'] . '?menu=4&amp;submenu=1&amp;m3=1');

        $smarty->assign('period', $Period[P_PERIOD]);

		if ($state == BILL_COMPLETE)
			$temp = '';
		else
			$temp = ' disabled="disabled"';
        $smarty->assign('complete', $temp);
        
        $smarty->assign('menu', $_REQUEST['menu']);
        $smarty->assign('submenu', $_REQUEST['submenu']);

        $smarty->display('customers/billing/bill/step1.tpl');
	}

	function subsubsubmit()
	{
		global $Period, $message;
		global $err;

		if ($_POST['action'] == 'Close Period')
		{
			set_globalConfig('billing-period', $Period[PN_PERIOD]);
			set_globalConfig('billing-status', BILL_PENDING);
			$Period = gen_periodArray();
			audit('Incremented billing period. It was ' . iid2title($Period[PP_PERIOD], true)
					. ', now is ' . iid2title($Period[P_PERIOD], true) . '.');
			$message = '<span>Billing period updated.</span>';
		}
	}

?>
