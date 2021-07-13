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
	// Handle edit customer bills page display
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

						. '<td>'
						. BillEditLink($bill->cid, $bill->iid, '../../')
						. '<img src="../../img/edit.png" alt="Edit' . $alt . '" title="Edit' . $alt . '" />'
						. '</a>'
						. '</td>';

				$href = $_SERVER['PHP_SELF'] . '?cid=' . $customer->id . '&iid=' . $bill->iid . '&menu=' . IDM_BILLS . '&action=db&offset=' . $offset . '&limit=' . $limit;
				$billsHtml .= '<td>'
						. '<a href="' . $href . '" alt="Delete' . $alt . '" title="Delete' . $alt . '">'
						. '<img src="../../img/delete.png" alt="Delete' . $alt . '" title="Delete' . $alt . '" />'
						. '</a>'
						. '</td>';

				$billsHtml .= '<td>' . iid2title($bill->iid) . '</td>'
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
			$class = 'ruled ';

		if ($offset == 0)
			$disabled = ' disabled="disabled"';
		else
			$disabled = '';
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
				<input type="submit" name="action" value="Add New" onclick="<?php echo BillAddUrl($customer->id, '../../'); ?>; return false;" />
			</div>
			<table>
				<thead>
					<tr>
						<th colspan="3"><?php echo $billsCount ?></th>
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
	// Return edit customer bills page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle edit customer bills page submits
	function submit()
	{
		global $message;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $customer, $Period;

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
		else if ($action != 'db')
			return;

		// If no period id, then nothing to do
		if (!isset($_GET['iid']))
		{
			$message = '<span>Delete bill failed (parameters were missing)!</span>';
			return;
		}

		// Make sure period appears valid
		if (!preg_match('/^0*[1-9]{1}[[:digit:]]{0,7}$/', $_GET['iid']))
		{
			$message = '<span>Delete bill failed, as Period ID was in invalid format</span>';
			return;
		}
		$IID = intval($_GET['iid']);

		// Delete the bill
		$query = "DELETE FROM `customers_bills` WHERE `cid` = " . $customer->id . " AND `iid` = " . $IID . " LIMIT 1";
		$result = db_query($query);
		if (!$result)
		{
			db_query(SQL_ROLLBACK);
			$message = '<span>Bill for ' . iid2title($IID) . ' not deleted!</span>';
			return;
		}
		else
		{
			$_POST['offset'] = $_GET['offset'];
			$_POST['limit'] = $_GET['limit'];

			// Note the success
			$message = '<span>Bill for ' . iid2title($IID) . ' deleted.</span>';

			audit('Deleted bill for ' . iid2title($IID) . ' (id = ' . sprintf('%04d', $IID)
					. ') from customer ' . sprintf('%06d', $customer->id) . '.');
		}
	}

?>
