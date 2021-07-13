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

	//-------------------------------------------------------------------------
	// Handle edit customer payments page display
	function display()
	{
		global $customer;
		global $err, $errCode, $errContext, $errQuery, $errText;

		$paymentsHtml = '';
		$paymentsCount = 0;

		if (isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = 12;

		if (isset($_REQUEST['offset']))
			$offset = intval($_REQUEST['offset']);
		else
			$offset = 0;

		$errObj = new error_stack();

		$query = "SELECT * FROM `customers_payments` WHERE `customer_id` = " . $customer->id . " ORDER BY `period_id` DESC LIMIT "
				. $offset . "," . $limit;
		$payments = db_query($query);
		if (!$payments)
			return;
		$paymentsCount = $payments->num_rows;
		if ($paymentsCount > 0)
		{
			while ($payment = $payments->fetch_object())
			{
				$pid = sprintf('P%08d', $payment->id);
				$alt = ' payment ' . $pid;
				$paymentsHtml .= '<tr>'
						. '<td>'
						. CustomerPaymentViewLink($payment->id, '../../')
						. '<img src="../../img/view.png" alt="View' . $alt . '" title="View' . $alt . '" />'
						. '</a>'
						. '</td>'

						. '<td>'
						. CustomerPaymentEditLink($payment->id, '../../')
						. '<img src="../../img/edit.png" alt="Edit' . $alt . '" title="Edit' . $alt . '" />'
						. '</a>'
						. '</td>';

				$href = $_SERVER['PHP_SELF'] . '?cid=' . $_REQUEST['cid'] . '&pid=' . $payment->id . '&menu=' . IDM_PAYMENTS . '&action=dp';
				$paymentsHtml .= '<td>'
						. '<a href="' . $href . '" alt="Delete' . $alt . '" title="Delete' . $alt . '">'
						. '<img src="../../img/delete.png" alt="Delete' . $alt . '" title="Delete' . $alt . '" />'
						. '</a>'
						. '</td>';

				$paymentsHtml .= '<td>' . strftime('%m/%d/%Y', strtotime($payment->created)) . '</td>'
						. '<td>' . iid2title($payment->period_id) . '</td>'
						. '<td>' . htmlspecialchars($payment->type) . '</td>'
						. '<td>' . valid_text($payment->extra1) . '</td>'
						. '<td>' . sprintf("$%01.2f", $payment->amount) . '</td>'
						. '<td>' . sprintf("$%01.2f", $payment->tip) . '</td>'
						. '</tr>';
			}
		}
		else
			$paymentsHtml = '<tr><td colspan="9">None</td></tr>';

		if ($paymentsCount == 0)
			$class = '';
		else
			$class = 'ruled ';
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
				<input type="submit" name="action" value="Add New" onclick="<?php echo CustomerPaymentAddUrl($customer->id, '../../'); ?>; return false;" />
			</div>
			<table>
				<thead>
					<tr>
						<th colspan="3"><?php echo $paymentsCount ?></th>
						<th>When</th>
						<th>Applied In</th>
						<th>Type</th>
						<th>ID</th>
						<th>Amount</th>
						<th>Tip</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $paymentsHtml ?>
				</tbody>
			</table>
<?php
	}

	//-------------------------------------------------------------------------
	//
	function scripts()
	{
		return
'
<script language="JavaScript">
	window.onload = function()
			{
				tableruler();
			}
	function tableruler()
	{
		if (document.getElementById && document.createTextNode)
		{
			var tables = document.getElementsByTagName(\'table\');
			for (var i = 0; i < tables.length; ++i)
			{
				if(tables[i].className == \'ruledpayments\')
				{
					var trs = tables[i].getElementsByTagName(\'tr\');
					for(var j = 0; j < trs.length; ++j)
					{
						if(trs[j].parentNode.nodeName == \'TBODY\')
						{
							trs[j].onmouseover = function()
									{
										this.className=\'ruled\';
										return false;
									}
							trs[j].onmouseout = function()
									{
										this.className=\'\';
										return false;
									}
						}
					}
				}
			}
		}
	}
</script>
';
	}

	//-------------------------------------------------------------------------
	// Return edit customer payments page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle edit customer payments page submits
	function submit()
	{
		global $message;
		global $err, $errCode, $errContext, $errQuery, $errText;

		$action = $_REQUEST['action'];
		if ($action == '<')
		{
			$offset = intval($_REQUEST['offset']);
			$limit = intval($_REQUEST['limit']);
			if ($offset > 0)
				$offset -= $limit;
			if ($offset < 0)
				$offset = 0;
			$_REQUEST['offset'] = $offset;
			return;
		}
		else if ($action == '>')
		{
			$offset = intval($_REQUEST['offset']);
			$limit = intval($_REQUEST['limit']);
			$_REQUEST['offset'] = $offset + $limit;
			return;
		}
		else if ($action != 'dp')
			return;

		// If no payment id, then nothing to do
		if (!isset($_REQUEST['pid']))
		{
			$message = '<span>Delete payment failed, as no Paument ID provided!</span>';
			return;
		}

		// Make sure pid appears valid
		if (!preg_match('/^0*[1-9]{1}[[:digit:]]{0,7}$/', $_REQUEST['pid']))
		{
			$message = '<span>Delete payment failed, as Payment ID was in invalid format</span>';
			return;
		}
		$PID = intval($_REQUEST['pid']);

		// Lookup the payment
		$err = ERR_SUCCESS;
		$payment = lup_c_payment($PID);
		if ($err >= ERR_SUCCESS)
		{
			do
			{
				// Wrap changes in a transaction
				$result = db_query(SQL_TRANSACTION);
				if (!$result)
					break;

				// Delete the payment
				$query = "DELETE FROM `customers_payments` WHERE `id` = " . $payment->id . " LIMIT 1";
				$result = db_query($query);
				if (!$result)
					break;

				// Locate the last payment made by this customer
				$query = "SELECT `id` FROM `customers_payments` WHERE `customer_id` = " . $payment->customer_id
						. " ORDER BY `created` DESC LIMIT 1";
				$pid = db_query_result($query);
				if (!$pid)
					break;
				if (empty($pid))
					$pid = 'NULL';

				// Update the customer record
				$query = "UPDATE `customers` SET `balance` = `balance` + " . ($payment->amount - $payment->tip)
						. ", `lastPayment` = " . $pid
						. " WHERE `id` = " . $_REQUEST['cid'] . " LIMIT 1";
				$result = db_query($query);
				if (!$result)
					break;
				$err = ERR_SUCCESS;
			} while (false);

			if ($err >= ERR_SUCCESS)
			{
				// Update the database
				db_query(SQL_COMMIT);

				// Clear the payment id
				unset($_REQUEST['pid']);

				// Let user know what happened
				$message = '<span>Payment successfully deleted!</span>';

				audit('Deleted payment (id = ' . sprintf('%08d', $payment->id) . ') from customer '
						. sprintf('%06d', $payment->customer_id) . '.');
			}
			else
			{
				// Undo changes on failure
				db_query(SQL_ROLLBACK);

				// Let user know what happened
				$message = '<span>Payment was NOT deleted!</span>';
			}
		}
	}

?>
