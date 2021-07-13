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
	// Handle view customer changes page display
	function display()
	{
		global $customer;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $DeliveryTypes;

		$changesHtml = '';
		$changesCount = 0;

		if (isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = 12;

		if (isset($_REQUEST['offset']))
			$offset = intval($_REQUEST['offset']);
		else
			$offset = 0;

		$errObj = new error_stack();

		// Get the changes for the user
		$query = "SELECT * FROM `customers_service_types` WHERE `customer_id` = " . $customer->id
				. " ORDER BY `when` DESC LIMIT "
				. $offset . ',' . $limit;
		$changes = db_query($query);
		if (!$changes)
			return;
		$changesCount = $changes->num_rows;
		if ($changesCount > 0)
		{
			while ($change = $changes->fetch_object())
			{
				$hid = sprintf('%08d', $change->id);
				$alt = ' change ' . $hid;
				$changesHtml .= '<tr>'
						. '<td>'
							. ServiceTypeViewLink($change->id, '../../')
								. '<img src="../../img/view.png" alt="V" title="View' . $alt . '" />'
							. '</a>'
						. '</td>';

				$changesHtml .= '<td>' . strftime("%m/%d/%Y", strtotime($change->when)) . '</td>';

				$changesHtml .= '<td>' . $DeliveryTypes[$change->type_id_from]['abbr'] . '</td>';
				$changesHtml .= '<td>' . $DeliveryTypes[$change->type_id_to]['abbr'] . '</td>';

				;
				if (is_null($change->period_id))
					$changesHtml .= '<td><span>pending</span></td>';
				else
					$changesHtml .= '<td>' . iid2title($change->period_id) . '</td>';
				$changesHtml .= '</tr>';
			}
		}
		else
			$changesHtml = '<tr><td colspan="11">None</td></tr>';

		unset($errObj);

		if ($changesCount == 0)
			$class = '';
		else
			$class = 'ruled ';
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
			</div>
			<table>
				<thead>
					<tr>
						<th><?php echo $changesCount ?></th>
						<th>When</th>
						<th>Old</th>
						<th>New</th>
						<th>Billed In</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $changesHtml ?>
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
				if(tables[i].className == \'ruledchanges\')
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
	// Return view customer changes page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle view customer changes page submits
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
