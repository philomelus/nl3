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
	// Handle view customer payments page display
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

		$query = "SELECT * FROM `customers_payments` WHERE `customer_id` = " . $customer->id
				. " ORDER BY `period_id` DESC LIMIT " . $offset . "," . $limit;
		$payments = db_query($query);
		if (!$payments)
			return;
		$paymentsCount = $payments->num_rows;
		if ($paymentsCount > 0)
		{
			while ($payment = $payments->fetch_object())
			{
				$pid = sprintf('%08d', $payment->id);
				$alt = ' payment ' . $pid;
				$paymentsHtml .= '<tr>'
						. '<td>'
						. CustomerPaymentViewLink($payment->id, '../../')
						. '<img src="../../img/view.png" alt="View' . $alt . '" title="View' . $alt . '" />'
						. '</a>'
						. '</td>'

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
			$paymentsHtml = '<tr><td colspan="9">None</td></tr>';

		if ($paymentsCount == 0)
			$class = '';
		else
			$class = 'ruled ';
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
			</div>
			<table>>
				<thead>
					<tr>
						<th><?php echo $paymentsCount ?></th>
						<th>When</th>
						<th>Period</th>
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
	// Return view customer payments page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer payments page submits
	function submit()
	{
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
	}

?>
