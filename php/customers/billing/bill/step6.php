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
        global $err, $resultHtml, $Period;
        
        if (!isset($_POST['sort']))
            $_POST['sort'] = '0';

        $temp = intval($_POST['sort']);
        $sort = array(0 => '', 1 => '', 2 => '');
        if ($temp < 0)
            $temp = 0;
        if ($temp > 2)
            $temp = 0;
        $sort[$temp] = ' checked="checked"';
        $smarty->assign('sort', $sort);

        if (!isset($_POST['type']))
            $_POST['type'] = 'all';

        $temp = $_POST['type'];
        if ($_POST['type'] == 'all')
            $type = array(0=>' checked="checked"', 1=>'');
        else if ($_POST['type'] == 'list')
            $type = array(0=>'', 1=>' checked="checked"');
        else
            $type = array(0=>' checked="checked"', 1=>'');
        $smarty->assign('type', $type);
        
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
        if ($state >= BILL_COMBINED)
            $temp = '';
        else
            $temp = ' disabled="disabled"';
        $smarty->assign('generated', $temp);
        $smarty->assign('period', $Period[P_PERIOD]);
        $smarty->assign('action', $_SERVER['PHP_SELF'] . '?menu=4&amp;submenu=1&amp;m3=6');

        $smarty->display('customers/billing/bill/step6.tpl');
    }

	function subsubsubmit()
	{
		global $Period, $message;

		if ($_POST['action'] == 'Download')
		{
			$temp = '?sort=' . intval($_POST['sort']);
			if ($_POST['type'] == 'list')
				$temp = '&ids=' . $_POST['ids'];
			header('Location: tools/bills_export.php' . htmlspecialchars($temp));
			exit();
		}
	}
?>
