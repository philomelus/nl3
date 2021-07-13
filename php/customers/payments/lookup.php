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
	require_once 'inc/popups/customers/payments.inc.php';
	
	//-------------------------------------------------------------------------

	define('PAYMENT_ANY', 'ANY');
	
	function subdisplay()
	{
		global $smarty, $Routes;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $resultHtml;

        $smarty->assign('action', $_SERVER['PHP_SELF']);
        $val = array
		(
			'lt' => '',
			'eq' => '',
			'gt' => ''
        );
        if (isset($_REQUEST['amountOp']) && !empty($_REQUEST['amountOp']))
            $val[$_REQUEST['amountOp']] = ' selected="selected"';
        else
            $val['eq'] = ' selected="selected"';
        $smarty->assign('amountOp', $val);

        if (isset($_REQUEST['amount']) && strlen($_REQUEST['amount']) > 0)
            $val = sprintf('%01.2f', floatval($_REQUEST['amount']));
        else
            $val = '';
        $smarty->assign('amount', $val);

        $val = array
            (
                'lt' => '',
                'eq' => '',
                'gt' => ''
            );
        if (isset($_REQUEST['tipOp']) && !empty($_REQUEST['tipOp']))
            $val[$_REQUEST['tipOp']] = ' selected="selected"';
        else
            $val['eq'] = ' selected="selected"';
        $smarty->assign('tipOp', $val);

        if (isset($_REQUEST['tip']) && strlen($_REQUEST['tip']) > 0)
            $val = sprintf('%01.2f', floatval($_REQUEST['tip']));
        else
            $val = '';
        $smarty->assign('tip', $val);

    	if (isset($_REQUEST['id']) && strlen($_REQUEST['id']) > 0)
	    	$val = htmlspecialchars(stripslashes($_REQUEST['id']));
	    else
	    	$val = '';
        $smarty->assign('id', $val);

        if (isset($_REQUEST['iid']) && intval($_REQUEST['iid']) > 0)
            $val = intval($_REQUEST['iid']);
        else
            $val = 0;
        $smarty->assign('iid', $val);

        if (isset($_REQUEST['pid']) && strlen($_REQUEST['pid']) > 0)
            $val = intval($_REQUEST['pid']);
        else
            $val = '';
        $smarty->assign('pid', $val);

        if (isset($_REQUEST['cid']) && strlen($_REQUEST['cid']) > 0)
            $val = intval($_REQUEST['cid']);
        else
            $val = '';
        $smarty->assign('cid', $val);

        $val = array();
        $val[0] = PAYMENT_ANY;
        $val[1] = PAYMENT_CHECK;
        $val[2] = PAYMENT_MONEYORDER;
        $val[3] = PAYMENT_CASH;
        $smarty->assign('typeVal', $val);
        
        $val = array
            (
                PAYMENT_ANY => '',
                PAYMENT_CHECK => '',
                PAYMENT_MONEYORDER => '',
                PAYMENT_CASH => ''
            );
	    if (isset($_REQUEST['type']) && strlen($_REQUEST['type']) > 0)
		    $val[$_REQUEST['type']] = ' selected="selected"';
	    else
		    $val[PAYMENT_ANY] = ' selected="selected"';
        $temp = array();
        $temp[0] = $val[PAYMENT_ANY];
        $temp[1] = $val[PAYMENT_CHECK];
        $temp[2] = $val[PAYMENT_MONEYORDER];
        $temp[3] = $val[PAYMENT_CASH];
        $smarty->assign('type', $temp);

        if (isset($_REQUEST['notes']) && strlen($_REQUEST['notes']) > 0)
            $val = htmlspecialchars(stripslashes($_REQUEST['notes']));
        else
            $val = '';
        $smarty->assign('notes', $val);

        $fields = array('adm',
                        'add',
                        'ady',
                        'ath',
                        'bdm',
                        'bdd',
                        'bdy',
                        'bth');
        foreach($fields as $field)
        {
            if (isset($_REQUEST[$field]) && strlen($_REQUEST[$field]) > 0)
                $val = intval($_REQUEST[$field]);
            else
                $val = '';
            $smarty->assign($field, $val);
        }

        $fields = array('atm',
                        'ats',
                        'btm',
                        'bts');
        foreach($fields as $field)
        {
            if (isset($_REQUEST[$field]) && strlen($_REQUEST[$field]) > 0)
                $atm = sprintf('%02d', intval($_REQUEST[$field]));
            else
                $val = '';
            $smarty->assign($field, $val);
        }

		if (isset($_REQUEST['limit']) && is_numeric($_REQUEST['limit']))
			$val = intval($_REQUEST['limit']);
		else
			$val = 30;
        $smarty->assign('limit', $val);

		if (isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset']))
			$val = intval($_REQUEST['offset']);
		else
            $val = 0;
        $smarty->assign('offset', $val);

		$smarty->assign('result', $resultHtml);
		$smarty->assign('menu', $_REQUEST['menu']);
        $smarty->assign('submenu', $_REQUEST['submenu']);
        
        $smarty->display('customers/payments/lookup.tpl');
	}
	
	//-------------------------------------------------------------------------
	
	function subsubmit()
	{
		global $err, $errCode, $errContext, $errQuery, $errText;

		$action = $_REQUEST['action'];
		
		// Clearing fields?
		if ($action == 'Clear')
		{
			$_REQUEST['amount'] = '';
			$_REQUEST['amountOp'] = 'eq';
			$_REQUEST['tip'] = '';
			$_REQUEST['tipOp'] = 'eq';
			$_REQUEST['id'] = '';
			$_REQUEST['iid'] = 0;
			$_REQUEST['pid'] = '';
			$_REQUEST['cid'] = '';
			$_REQUEST['type'] = PAYMENT_ANY;
			$_REQUEST['notes'] = '';
			$_REQUEST['adm'] = '';
			$_REQUEST['add'] = '';
			$_REQUEST['ady'] = '';
			$_REQUEST['ath'] = '';
			$_REQUEST['atm'] = '';
			$_REQUEST['ats'] = '';
			$_REQUEST['bdm'] = '';
			$_REQUEST['bdd'] = '';
			$_REQUEST['bdy'] = '';
			$_REQUEST['bth'] = '';
			$_REQUEST['btm'] = '';
			$_REQUEST['bts'] = '';
			return '';
		}
		// Back a page?
		else if ($action == '<')
		{
			$limit = intval($_REQUEST['limit']);
			$offset = intval($_REQUEST['offset']);
			$offset -= $limit;
			if ($offset < 0)
				$offset = 0;
			$_REQUEST['offset'] = $offset;
		}
		// Forward a page?
		else if ($action == '>')
			$_REQUEST['offset'] += $_REQUEST['limit'];
		
		// Build query where clause
		$where = '';
		$and = 'WHERE';
		// amount
		if (strlen($_REQUEST['amount']) > 0)
		{
			switch ($_REQUEST['amountOp'])
			{
			case 'lt':	$op = '<';	break;
			case 'eq':	$op = '=';	break;
			case 'gt':	$op = '>';	break;
			default:	$op = '=';	break;
			}
			$where .= $and . " `amount` " . $op . " " . floatval($_REQUEST['amount']);
			$and = ' AND';
		}
		// tip
		if (strlen($_REQUEST['tip']) > 0)
		{
			switch ($_REQUEST['tipOp'])
			{
			case 'lt':	$op = '<';	break;
			case 'eq':	$op = '=';	break;
			case 'gt':	$op = '>';	break;
			default:	$op = '=';	break;
			}
			$where .= $and . " `tip` " . $op . " " . floatval($_REQUEST['tip']);
			$and = ' AND';
		}
		// id
		if (strlen($_REQUEST['id']) > 0)
		{
			$where .= $and . " `extra1` LIKE '%" . db_escape($_REQUEST['id']) . "%'";
			$and = ' AND';
		}
		// period
		if (intval($_REQUEST['iid']) != 0)
		{
			$where .= $and . " `period_id` = " . intval($_REQUEST['iid']);
			$and = ' AND';
		}
		// pid
		if (strlen($_REQUEST['pid']) > 0)
		{
			$where .= $and . " `id` = " . intval($_REQUEST['pid']);
			$and = ' AND';
		}
		// cid
		if (strlen($_REQUEST['cid']) > 0)
		{
			$where .= $and . " `customer_id` = " . intval($_REQUEST['cid']);
			$and = ' AND';
		}
		// type
		if ($_REQUEST['type'] != PAYMENT_ANY)
		{
			switch ($_REQUEST['type'])
			{
			case PAYMENT_CASH:			$val = 'CASH';			break;
			case PAYMENT_CHECK:			$val = 'CHECK';			break;
			case PAYMENT_MONEYORDER:	$val = 'MONEYORDER';	break;
			default:					$val = 'CHECK';			break;
			}
			$where .= $and . " `type` = '" . $val . "'";
			$and = ' AND';
		}
		// notes
		if (strlen($_REQUEST['notes']) > 0)
		{
			$words = explode(' ', stripslashes($_REQUEST['notes']));
			foreach($words as $word)
			{
				$where .= $and . " `note` LIKE '%" . db_escape(stripslashes($word)) . "%'";
				$and = ' AND';
			}
		}
		// when
		$before = strlen($_REQUEST['bdm']);
		$after = strlen($_REQUEST['adm']);
		if ($after > 0 || $before > 0)
		{
			if ($after > 0)
			{
				$date = valid_date('ad', 'After date');
				if ($err >= ERR_SUCCESS)
				{
					if (strlen($_REQUEST['ath']) > 0)
					{
						$time = valid_time('at', 'After date');
						if ($err >= ERR_SUCCESS)
						{
							$where .= $and . " `created` > '" . strftime('%Y-%m-%d %H:%M:%S', strtotime($date . ' ' . $time)) . "'";
							$and = ' AND';
						}
					}
					else
					{
						$where .= $and . " `created` > '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
						$and = ' AND';
					}
				}
			}
			if ($before > 0)
			{
				$date = valid_date('bd', 'Before date');
				if ($err >= ERR_SUCCESS)
				{
					if (strlen($_REQUEST['bth']) > 0)
					{
						$time = valid_time('bt', 'Before date');
						if ($err >= ERR_SUCCESS)
						{
							$where .= $and . " `created` < '" . strftime('%Y-%m-%d %H:%M:%S', strtotime($date . ' ' . $time)) . "'";
							$and = ' AND';
						}
					}
					else
					{
						$where .= $and . " `created` < '" . strftime('%Y-%m-%d', strtotime($date)) . "'";
						$and = ' AND';
					}
				}
			}
		}
		
		// Determine total number of records
		$query = "SELECT COUNT(*) FROM `customers_payments` " . $where;
		$count = db_query_result($query);
		if (!$count)
			return '';
		
		// Get payment records
		$query = "SELECT * FROM `customers_payments` " . $where
				. " ORDER BY `period_id` DESC, `customer_id` ASC LIMIT "
				. intval($_REQUEST['offset']) . "," . intval($_REQUEST['limit']);
		$payments = db_query($query);
		if (!$payments)
			return '';
		
		// Generate table header
		$html = '<hr />'
				. '<br />'
				. '<table>'
				. '<thead>'
				. '<tr>'
				. '<th colspan="3">' . $count . '</th>'
				. '<th>CID</th>'
				. '<th>When</th>'
				. '<th>Period</th>'
				. '<th>Type</th>'
				. '<th>ID</th>'
				. '<th>Amount</th>'
				. '<th>Tip</th>'
				. '</tr>'
				. '</thead>'
				. '<tbody>';
		
		// Generate formatted payments
		if ($count > 0)
		{
			while ($payment = $payments->fetch_object())
			{
				$pid = sprintf('%08d', $payment->id);
				$alt = ' payment ' . $pid;
				$html .= '<tr>'
						. '<td>'
						. CustomerPaymentViewLink($payment->id)
						. '<img src="img/view.png" alt="View' . $alt . '" title="View' . $alt . '" />'
						. '</a>'
						. '</td>';
				
				$html .= '<td>'
						. CustomerPaymentEditLink($payment->id)
						. '<img src="img/edit.png" alt="Edit' . $alt . '" title="Edit' . $alt . '" />'
						. '</a>'
						. '</td>';

				$href = $_SERVER['PHP_SELF'] . '?cid=' . $_REQUEST['cid'] . '&pid=' . $payment->id . '&menu=' . IDSM_PAYMENTS . '&submenu=' . IDSSM_LOOKUP . '&action=dp';
				$html .= '<td>'
						. '<a href="' . $href . '" alt="Delete' . $alt . '" title="Delete' . $alt . '">'
						. '<img src="img/delete.png" alt="Delete' . $alt . '" title="Delete' . $alt . '" />' 
						. '</a>'
						. '</td>';

						
				$html .= '<td>' . CustomerViewLink($payment->customer_id) . sprintf('%06d', $payment->customer_id) . '</a></td>'
						
						. '<td>' . strftime('%m/%d/%Y', strtotime($payment->created)) . '</td>'
						
						. '<td>' . iid2title($payment->period_id) . '</td>'
						
						. '<td>' . htmlspecialchars($payment->type) . '</td>'
						
						. '<td>' . valid_text($payment->extra1) . '</td>'
						
						. '<td>' . sprintf("$%01.2f", $payment->amount) . '</td>'
						
						. '<td>' . sprintf("$%01.2f", $payment->tip) . '</td>'
						
						. '</tr>';
			}
		}
		else
			$html .= '<tr><td colspan="10">None</td></tr>';

		// Close it all up and return it
		return $html
				. '</tbody>'
				. '</table>';
	}

?>
