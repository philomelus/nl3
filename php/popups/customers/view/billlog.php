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

		$html = '';

		if (isset($_REQUEST['limit']))
			$limit = intval($_REQUEST['limit']);
		else
			$limit = 10;

		if (isset($_REQUEST['offset']))
			$offset = intval($_REQUEST['offset']);
		else
			$offset = 0;

		$errObj = new error_stack();

		$query = "SELECT COUNT(*) FROM `customers_bills_log` WHERE `customer_id` = " . $customer->id;
		$count = db_query_result($query);
		if (!$count)
			$count = 0;

		$query = "SELECT * FROM `customers_bills_log` WHERE `customer_id` = " . $customer->id
				. " ORDER BY `when` DESC, `sequence` DESC LIMIT " . $offset . ',' . $limit;
		$logs = db_query($query);
		if (!$logs)
		{
			echo gen_error();
			return;
		}
		if ($logs->num_rows > 0)
		{
			while ($log = $logs->fetch_object())
			{
			$html .= '<tr>'
					. '<td>' . strftime('%m/%d/%Y %H:%M:%S', strtotime($log->when)) . '</td>'
					. '<td>' . iid2title($log->period_id) . '</td>'
					. '<td>' . $log->what . '</td>'
					. '</tr>';
			}
		}
		else
			$html = '<tr><td colspan="6">None</td></tr>';
?>
			<div>
<?php echo gen_dbFields(0, 10, '../../'); ?>
			</div>
			<table>
				<thead>
					<tr>
						<th>When</th>
						<th>Period</th>
						<th>Message</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $html ?>
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
