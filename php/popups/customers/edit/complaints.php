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
	// Handle edit customer changes page display
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
		$query = "SELECT * FROM `customers_complaints` WHERE `customer_id` = " . $customer->id
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
						. ComplaintViewLink($change->id, '../../')
						. '<img src="../../img/view.png" alt="V" title="View' . $alt . '" />'
						. '</a>'
						. '</td>'

						. '<td>'
						. ComplaintEditLink($change->id, '../../')
						. '<img src="../../img/edit.png" alt="E" title="Edit' . $alt . '" />'
						. '</a>'
						. '</td>';

				$href = $_SERVER['PHP_SELF'] . '?cid=' . $_REQUEST['cid'] . '&menu=' . IDM_COMPLAINTS . '&hid=' . $change->id . '&action=dc';
				$changesHtml .= '<td>'
						. '<a href="' . $href . '" alt="Delete' . $alt . '" title="Delete' . $alt . '">'
						. '<img src="../../img/delete.png" alt="Delete' . $alt . '" title="Delete' . $alt . '" />'
						. '</a>'
						. '</td>';

				switch ($change->type)
				{
				case BITCH_MISSED:
				case BITCH_WET:
				case BITCH_DAMAGED:
					$changesHtml .= '<td>' . $change->type . '</td>';
					break;

				default:
					$changesHtml .= '<td><span>UNKNOWN - ' . $change->type . '</span></td>';
					break;
				}

				$changesHtml .= '<td>' . strftime("%m/%d/%Y", strtotime($change->when)) . '</td>';

				// Result
				$changesHtml .= '<td>';
				switch ($change->result)
				{
				case RESULT_NOTHING:
					$changesHtml .= 'Nothing';
					break;

				case RESULT_CREDIT1DAILY:
					$changesHtml .= "Credit 1 Daily";
					break;

				case RESULT_CREDIT1SUNDAY:
					$changesHtml .= "Credit 1 Sunday";
					break;

				case RESULT_REDELIVERED:
					$changesHtml .= "Redelivered";
					break;

				case RESULT_CREDIT:
					$changesHtml .= "Credit";
					break;

				case RESULT_CHARGE:
					$changesHtml .= "Charged";
					break;

				default:
					$changesHtml .= "??? ERROR ???";
					break;
				}
				$changesHtml .= "</td>";

				// Amount
				$changesHtml .= '<td>';
				switch ($change->result)
				{
				case RESULT_CREDIT1DAILY:
					// TODO:  If period = 0, then this, otherwise value of $change->amount
//					$changesHtml .= htmlspecialchars(sprintf("$%01.2f", $DeliveryTypes[$customer->type_id]['Mon']['credit']));
					$changesHtml .= '(bill)';
					break;

				case RESULT_CREDIT1SUNDAY:
					// TODO:  If period = 0, then this, otherwise value of $change->amount
//					$changesHtml .= htmlspecialchars(sprintf("$%01.2f", $DeliveryTypes[$customer->type_id]['Sun']['credit']));
					$changesHtml .= '(bill)';
					break;

				case RESULT_NOTHING:
				case RESULT_REDELIVERED:
					$changesHtml .= 'n/a';
					break;

				case RESULT_CREDIT:
				case RESULT_CHARGE:
					$changesHtml .= htmlspecialchars(sprintf("$%01.2f", $change->amount));
					break;

				default:
					$changesHtml .= "??? ERROR ???";
					break;
				}
				$changesHtml .= "</td>";
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
				<input type="submit" name="action" value="Add New" onclick="<?php echo ComplaintAddUrl($customer->id, '../../') ?>" />
			</div>
			<table>
				<thead>
					<tr>
						<th colspan="3"><?php echo $changesCount ?></th>
						<th>Type</th>
						<th>When</th>
						<th>Result</th>
						<th>Amount</th>
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
<script type="text/javascript" src="../../js/popups/customers/complaints.js.php"></script>
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
	// Return edit customer changes page specific styles
	function styles()
	{
        return '';
	}

	//-------------------------------------------------------------------------
	// Handle edit customer changes page submits
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
		else if ($action != 'dc')
			return;

		// If no change id, then nothing to do
		if (!isset($_REQUEST['hid']))
		{
			$message = '<span>Delete change failed, as no Change ID provided!</span>';
			return;
		}

		// Make sure hid appears valid
		if (!preg_match('/^0*[1-9]{1}[[:digit:]]{0,7}$/', $_REQUEST['hid']))
		{
			$message = '<span>Delete change failed, as Change ID was in invalid format</span>';
			return;
		}
		$HID = intval($_REQUEST['hid']);

		// Lookup the change
		$errObj = new error_stack();
		$change = lup_c_complaint($HID);
		if ($err < ERR_SUCCESS)
			return;

		// Wrap changes in a transaction
		if (!db_query(SQL_TRANSACTION))
			return;

		// Delete the change record
		$query = "DELETE FROM `customers_complaints` WHERE `id` = " . $HID . " LIMIT 1";
		$result = db_query($query);

		if ($err >= ERR_SUCCESS)
		{
			// Update the database
			db_query(SQL_COMMIT);

			// Clear change id
			unset($_REQUEST['hid']);

			// Note the success
			$message = '<span>Change ' . sprintf('%08d', $HID)
					. ' deleted successfully!</span>';

			audit('Deleted complaint (id = ' . sprintf('%08d', $HID) . ') from customer '
					. sprintf('%06d', $customer->id) . '.');
		}
		else
		{
			// Undo changes on failure
			db_query(SQL_ROLLBACK);

			// Note the failure
			$message = '<span>Change ' . sprintf('H%08d', $HID)
					. ' not deleted!</span>';
		}
	}

?>
