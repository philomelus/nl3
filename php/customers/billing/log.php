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
	require_once 'inc/popups/customers.inc.php';
	require_once 'inc/popups/periods.inc.php';

	function subdisplay()
    {
        global $smarty;
		global $resultHtml, $smarty;
		global $Period;
		
		if (!isset($_REQUEST['count']))
			$_REQUEST['count'] = '30';
		if (!isset($_REQUEST['offset']))
			$_REQUEST['offset'] = '0';
        $smarty->assign('count', $_REQUEST['count']);
        $smarty->assign('offset', $_REQUEST['offset']);

		if (isset($_REQUEST['cid']))
			$CID = intval($_REQUEST['cid']);
		else
			$CID = '';
        $smarty->assign('CID', $CID);

        $smarty->assign('action', $_SERVER['PHP_SELF']);

		if (isset($_REQUEST['iid']) && intval($_REQUEST['iid']) > 0)
			$val = intval($_REQUEST['iid']);
		else
			$val = $Period[P_PERIOD];
        $smarty->assign('iid', $val);

		if (isset($_REQUEST['date']))
			$val = $_REQUEST['date'];
		else
			$val = '&gt;';
        if ($val == '&gt;')
        {
            $generated = array(0 => ' selected="selected"',
                               1 => '');
        }
        else
        {
            $generated = array(0 => '',
                               1 => ' selected="selected"');
        }
        $smarty->assign('generated', $generated);

        $fields = array('datem' => 'month',
                        'dated' => 'day',
                        'datey' => 'year',
                        'timeh' => 'hour',
                        'timem' => 'minute',
                        'times' => 'second');
        foreach($fields as $req => $name)
        {
            if (isset($_REQUEST[$req]) && !empty($_REQUEST[$req]))
                $val = $_REQUEST[$req];
            else
                $val = '';
            $smarty->assign($name, $val);
        }

		if (isset($_REQUEST['failed']) && $_REQUEST['failed'] == 1)
			$val = ' checked="checked"';
		else
			$val = '';
        $smarty->assign('failures', $val);

        // TODO:  Convert the count/offset into gen_db_fields/{db_fields}
		if (isset($_REQUEST['count']) && intval($_REQUEST['count']) > 0)
			$val = intval($_REQUEST['count']);
		else
			$val = 30;
        $smarty->assign('count', $val);

		if (isset($_REQUEST['offset']) && intval($_REQUEST['offset']) > 0)
			$val = intval($_REQUEST['offset']);
		else
            $val = 0;
        $smarty->assign('offset', $val);

		$smarty->assign('menu', $_REQUEST['menu']);
		$smarty->assign('submenu', $_REQUEST['submenu']);
        $smarty->assign('result', $resultHtml);

        $smarty->display('customers/billing/log.tpl');
	}

	function subsubmit()
	{
		global $err;
		
		if ($_REQUEST['action'] == '&lt;')
		{
			$offset = intval($_REQUEST['offset']);
			$amount = intval($_REQUEST['count']);
			if ($offset > 0)
				$offset -= $amount;
			if ($offset < 0)
				$offset = 0;
			$_REQUEST['offset'] = $offset;
		}
		
		if ($_REQUEST['action'] == '&gt;')
			$_REQUEST['offset'] = intval($_REQUEST['offset']) + intval($_REQUEST['count']);
		
		$query = "SELECT * FROM `customers_bills_log`";
		$where = 'WHERE';
		$and = '';
		if (isset($_REQUEST['cid']) && intval($_REQUEST['cid']) > 0)
		{
			$query .= $where . $and . ' `customer_id` = ' . intval($_REQUEST['cid']);
			$and = ' AND';
			$where = '';
		}
		if (isset($_REQUEST['iid']) && intval($_REQUEST['iid']) > 0)
		{
			$query .= $where . $and . ' `period_id` = ' . intval($_REQUEST['iid']);
			$and = ' AND';
			$where = '';
		}
		if (isset($_REQUEST['datem']) && !empty($_REQUEST['datem']))
		{
			$date = valid_date('date', 'Date');
			if ($err >= ERR_SUCCESS)
			{
				if (empty($_REQUEST['timeh']))
					$_REQUEST['timeh'] = '0';
				if (empty($_REQUEST['timem']))
					$_REQUEST['timem'] = '0';
				if (empty($_REQUEST['times']))
					$_REQUEST['times'] = '0';
				$time = valid_time('time', 'Time');
				if ($err >= ERR_SUCCESS)
				{
					$query .= $where . $and . ' `when` ' . $_REQUEST['date'] . " '"
							. strftime('%Y-%m-%d %H:%M:%S', strtotime($date . ' ' . $time)) . "'";
					$and = ' AND';
					$where = '';
				}
			}
		}
		if (isset($_REQUEST['failed']) && $_REQUEST['failed'] == 1)
		{
			$query .= $where . $and . " `what` LIKE 'BILL GENERATION FAILED!'";
			$and = ' AND';
			$where = '';
		}
		$query .= " ORDER BY `customer_id`, `period_id`, `sequence` LIMIT " . intval($_REQUEST['start']) . ',' . intval($_REQUEST['count']);
		$records = db_query($query);
		if (!$records)
			return '';
		$html = '<div></div>'
				. '<table>'
				. '<tr>'
				. '<th>When</th>'
				. '<th>Cust. ID</th>'
				. '<th>Period</th>'
				. '<th>Message</th>'
				. '</tr>';
		while ($log = $records->fetch_object())
		{
			$html .= '<tr>'
					. '<td>' . strftime('%m/%d/%Y %H:%M:%S', strtotime($log->when)) . '</td>'
					. '<td>' . gen_customerid($log->customer_id, SCB_LOG) . '</td>'
					. '<td>' . gen_periodid($log->period_id, SCB_LOG) . '</td>'
					. '<td>' . $log->what . '</td>'
					. '</tr>';
					
		}
		$html .= '</table>';
		return $html;
	}
?>
