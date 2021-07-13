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
	// Handle view customer bills page display
	function display()
	{
		global $customer;
		global $err, $errCode, $errContext, $errQuery, $errText;

		$billsCount = 0;
		$billsHtml = '';

		if (isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = 12;

		if (isset($_REQUEST['offset']))
			$offset = intval($_REQUEST['offset']);
		else
			$offset = 0;

		// Get this customers bills
		$query = "SELECT * FROM `customers_bills` WHERE `cid` = '" . sprintf('%06d', intval($customer->id))
				. "' ORDER BY `iid` DESC LIMIT " . $offset . "," . $limit;
		$bills = db_query($query);
		if (!$bills)
			return;
		$billsCount = $bills->num_rows;
		if ($billsCount > 0)
		{
			while ($bill = $bills->fetch_object())
			{
				$bid = sprintf('B%s%04d', $bill->cid, $bill->iid);
				$alt = ' bill ' . $bid;
				$billsHtml .= '<tr>'
						. '<td>'
						. BillViewLink($bill->cid, $bill->iid, '../../')
						. '<img src="../../img/view.png" alt="View' . $alt . '" title="View' . $alt . '" />'
						. '</a>'
						. '</td>'
						. '<td>' . iid2title($bill->iid) . '</td>'
						. '<td>' . $bill->rTit . '</td>'
						. '<td>' . currency_text($bill->fwd) . '</td>'
						. '<td>' . currency_text($bill->pmt, true) . '</td>'
						. '<td>' . currency_text($bill->rate) . '</td>'
						. '<td>' . currency_text($bill->adj) . '</td>'
						. '<td>' . currency_text($bill->bal) . '</td>'
						. '</tr>';
			}
		}
		else
			$billsHtml = '<tr><td colspan="10">None</td></tr>';


		if ($billsCount == 0)
			$class = '';
		else
			$class = 'ruled';
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
			</div>
			<table>
				<thead>
					<tr>
						<th><?php echo $billsCount ?></th>
						<th>Period</th>
						<th>Type</th>
						<th>Prev</th>
						<th>Pmts</th>
						<th>Rate</th>
						<th>Adj</th>
						<th>Balance</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $billsHtml ?>
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
				if(tables[i].className == \'ruledbills\')
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
	// Return view customer bills page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer bills page submits
	function submit()
	{
		$action = htmlentities($_REQUEST['action']);
		if (preg_match('/&laquo;/', $action))
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
		else if (preg_match('/&raquo;/', $action))
		{
			$offset = intval($_REQUEST['offset']);
			$limit = intval($_REQUEST['limit']);
			$_REQUEST['offset'] = $offset + $limit;
			return;
		}
	}

?>
