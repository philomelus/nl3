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
	// Handle view customer adjustments page display
	function display()
	{
		global $customer;
		global $err, $errCode, $errContext, $errQuery, $errText;

		$adjustmentsHtml = '';
		$adjustmentsCount = 0;

		if (isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = 12;

		if (isset($_REQUEST['offset']))
			$offset = intval($_REQUEST['offset']);
		else
			$offset = 0;

		$errObj = new error_stack();

		$query = "SELECT * FROM `customers_adjustments` WHERE `customer_id` = " . $customer->id
				. " ORDER BY `period_id` DESC, `created` DESC, `updated` DESC LIMIT "
				. $offset . ',' . $limit;
		$adjustments = db_query($query);
		if (!$adjustments)
			return;
		$adjustmentsCount = $adjustments->num_rows;
		if ($adjustmentsCount > 0)
		{
			while ($adjustment = $adjustments->fetch_object())
			{
				$aid = sprintf('%08d', $adjustment->id);
				$alt = ' adjustment ' . $aid;
				$adjustmentsHtml .= '<tr>'
						. '<td>'
						. AdjustmentViewLink($adjustment->id, '../../')
						. '<img src="../../img/view.png" alt="View' . $alt . '" title="View' . $alt . '" />'
						. '</a>'
						. '</td>';

				$adjustmentsHtml .= '<td>' . (is_null($adjustment->period_id)
                        ? '<span>pending</span>' : iid2title($adjustment->period_id)) . '</td>'

						. '<td>' . valid_text(htmlspecialchars(stripslashes($adjustment->desc))) . "</td>"

						. '<td>' . currency($adjustment->amount) . '</td>'

						. '</tr>';
			}
		}
		else
			$adjustmentsHtml = '<tr><td colspan="6">None</td></tr>';
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
			</div>
			<table>
				<thead>
					<tr>
						<th><?php echo $adjustmentsCount ?></th>
						<th>Period</th>
						<th>Description</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $adjustmentsHtml ?>
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
				if(tables[i].className == \'ruledadjustments\')
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
	// Return view customer adjustments page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer adjustments page submits
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
