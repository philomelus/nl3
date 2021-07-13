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
	// Handle edit customer adjustments page display
	function display()
	{
		global $customer;

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

		$query =
"
SELECT
 *
FROM
 `customers_adjustments`
WHERE
 `customer_id` = " . $customer->id . "
ORDER BY
 `period_id` DESC,
 `created` DESC,
 `updated` DESC
LIMIT " . $offset . ',' . $limit;
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
						. '</td>'

						. '<td>'
						. AdjustmentEditLink($adjustment->id, '../../')
						. '<img src="../../img/edit.png" alt="Edit' . $alt .'" title="Edit' . $alt .'" />'
						. '</a>'
						. '</td>';

				$href = $_SERVER['PHP_SELF'] . '?cid=' . $_REQUEST['cid'] . '&aid=' . $adjustment->id . '&action=da&menu=' . IDM_ADJUSTMENTS;
				$adjustmentsHtml .= '<td>'
						. '<a href="' . $href . '" alt="Delete' . $alt .'" title="Delete' . $alt .'">'
						. '<img src="../../img/delete.png" alt="Delete' . $alt .'" title="Delete' . $alt . '" />'
						. '</a>'
						. '</td>';

				$adjustmentsHtml .= '<td>' . valid_text(htmlspecialchars(stripslashes($adjustment->desc))) . "</td>"
						. '<td>' . currency($adjustment->amount) . '</td>';

				if (is_null($adjustment->period_id))
					$adjustmentsHtml .= '<td><span>pending</span></td>';
				else
					$adjustmentsHtml .= '<td>' . iid2title($adjustment->period_id) . '</td>';
				$adjustmentsHtml .= '</tr>';
			}
		}
		else
			$adjustmentsHtml = '<tr><td colspan="6">None</td></tr>';

		if ($adjustmentsCount == 0)
			$class = '';
		else
			$class = 'ruled ';

		$url = AdjustmentAddUrl($customer->id, '../../');
?>
			<div>
<?php echo gen_dbFields(0, 12, '../../'); ?>
				<input type="submit" name="action" value="Add New" onclick="<?php echo $url ?>; return false;" />
			</div>
			<table>
				<thead>
					<tr>
						<th colspan="3"><?php echo $adjustmentsCount ?></th>
						<th>Description</th>
						<th>Amount</th>
						<th>Billed In</th>
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
	// Return edit customer adjustments page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle edit customer adjustments page submits
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
		else if ($action != 'da')
			return;

		// If no adjustment id, then nothing to do
		if (!isset($_REQUEST['aid']))
		{
			$message = '<span>Delete adjustment failed, as no Adjustment ID provided!</span>';
			return;
		}

		// Make sure hid appears valid
		if (!preg_match('/^0*[1-9]{1}[[:digit:]]{0,7}$/', $_REQUEST['aid']))
		{
			$message = '<span>Delete adjustment failed, as Adjustment ID was in invalid format</span>';
			return;
		}
		$AID = intval($_REQUEST['aid']);

		// Delete the adjustment
		$query = 'DELETE FROM `customers_adjustments` WHERE `id` = ' . $_REQUEST['aid'] . ' LIMIT 1';
		$result = db_query($query);
		if (!$result)
		{
			$message = '<span>Adjustment ' . sprintf('A%08d', $AID)
					. ' not deleted!</span>';
			return;
		}
		else
		{
			// Clear adjustment id
			unset($_REQUEST['aid']);

			// Note the success
			$message = '<span>Adjustment ' . sprintf('A%08d', $AID)
					. ' deleted successfully!</span>';

			audit('Deleted adjustment (id = ' . sprintf('%08d', $AID) . ') from customer '
					. sprintf('%06d', $_REQUEST['cid']) . '.');
		}
	}

?>
