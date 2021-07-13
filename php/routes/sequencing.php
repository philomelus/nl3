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

	require_once 'inc/sql.inc.php';
	
	$CONTEXT = 'Routes / Sequencing';

	//-------------------------------------------------------------------------

	function display()
	{
		global $err, $smarty;
		global $resultHtml;

		// Determine correct route id
		if (isset($_POST['rid']))
			$rid = intval($_POST['rid']);
		else
			$rid = get_config('default-customer-route-id', 0);

		if ($err < ERR_SUCCESS)
			echo gen_error();

		$smarty->display("menu.tpl");

?>
	<form name="sequence" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<div>
			<div>
				Route
<?php
		echo gen_routesSelect('rid', $rid, false, '', '');
?>
				<input type="submit" name="action" value="Update Display" />
			</div>
			<div>
<?php
		if (isset($_POST['notes']) && intval($_POST['notes']) == 1)
			$val = ' checked="checked"';
		else
			$val = '';
?>
				<input type="checkbox" name="notes" value="1"<?php echo $val ?>>Display Delivery Notes?</input>
			</div>
		</div>
		<br />
		<?php echo $resultHtml ?>
		<input type="hidden" name="menu" value="<?php echo $_REQUEST['menu'] ?>" />
	</form>
<?php
	}

	//-------------------------------------------------------------------------

	function display_customer($id, &$odd, $divided, $delNotes, $resetNote, $resetNoteId)
	{
		global $DeliveryTypes, $err;

		$customer = lup_customer($id);
		if ($err < ERR_SUCCESS)
		{
            $tempErr = $err;
			$err = ERR_SUCCESS;
			return '<tr><td>&nbsp;</td>'
					. '<td>Cust.</td><td>'
					. CustomerViewLink($id) . sprintf('%08d', $id)
					. '</a></td><td colspan="4">Error (' . $tempErr . ') Prevents Display</td></tr>';
		}
		if ($customer->routeList == 'N')
			return '';
		if ($DeliveryTypes[$customer->type_id]['color'] == 0xFFFFFF)
			$class = ($odd ? 'odd' : 'even');
		else
		{
			$class = 'dt' . sprintf('%04d', $customer->type_id);
			$odd = false;
		}
		$html = '<tr>'
				. '<td>'
				. '<input type="radio" name="id" value="' . $customer->id . '"';
		if (isset($_POST['id']))
		{
			if (intval($_POST['id']) == intval($customer->id))
				$html .= ' checked="checked"';
		}
		$html .= ' />';
		if (!$divided)
		{
			$html .= '<button name="action" value="m' . sprintf('%08d', $customer->id)
					. '" type="submit">Move Here</button>';
		}
		$html .= '</td>';

		$html .= '<td>Cust.</td>'
				. '<td>'
				. CustomerViewLink($customer->id)
				. sprintf('%08d', $customer->id)
				. '</a>'
				. '</td>';

		if (strlen($customer->lastName) > 0)
			$html .= '<td>' . $customer->firstName . ' ' . $customer->lastName . '</td>';
		else
			$html .= '<td>' . $customer->firstName . '</td>';

		$html .= '<td>' . $customer->address . '</td>'
				. '<td>' . $DeliveryTypes[$customer->type_id]['abbr'] . '</td>';

		if ($delNotes)
		{
			$html .= '<td>'
					. '<textarea name="notes' . sprintf('%08d', $customer->id) . '" rows="1" cols="20">';
			if (isset($_POST['notes' . sprintf('%08d', $customer->id)]))
			{
				if ($resetNote && $resetNoteId == $customer->id)
					$html .= valid_text($customer->deliveryNote);
				else
					$html .= valid_text($_POST['notes' . sprintf('%08d', $customer->id)]);
			}
			else
				$html .= valid_text($customer->deliveryNote);
			$html .= '</textarea>'
					. '<br /><button name="action" value="s' . sprintf('%08d', $customer->id) . '" type="submit">Save</button>'
					. '<button name="action" value="r' . sprintf('%08d', $customer->id) . '" type="submit">Revert</button></td>';
		}

		$html .= '</tr>';
		return $html;
	}

	//-------------------------------------------------------------------------

	function submit()
	{
		global $err, $DB;

		$rid = intval($_POST['rid']);

		// Take appropriate action
		$html = '';
		$resetNote = false;
		$resetNoteId = 0;
		if (isset($_POST['id']) && !empty($_POST['id'])
				&& isset($_POST['action']) && strlen($_POST['action']) == 9
				&& preg_match('/^m[0-9]{8}$/', $_POST['action']))
		{
			do
			{
				// Assume success
				$err = ERR_SUCCESS;

				// Get the id's
				$who = intval($_POST['id']);
				$where = intval(substr($_POST['action'], 1));

				// Use a transaction so we can undo it on failure
				if (!db_query(SQL_TRANSACTION))
					break;

				// Remove the old order if needed.  If the order is 99999 then
				// its a new customer that hasn't been placed yet, so no need
				// to remove its sequence number.
				$order = db_query_result('SELECT `order` FROM `routes_sequence` WHERE `tag_id` = ' . $who);
				if (!$order)
					break;
				if ($order != 99999)
				{
					$query = 'UPDATE `routes_sequence` SET `order` = `order` - 1 WHERE `route_id` = '
							. $rid . ' AND `order` >= ' . $order . ' AND `order` < 99999';
					if (!db_query($query))
						break;
				}

				// Make room for the customer being moved
				$order = db_query_result('SELECT `order` FROM `routes_sequence` WHERE `tag_id` = ' . $where);
				if (!$order)
					break;
				$query = 'UPDATE `routes_sequence` SET `order` = `order` + 1 WHERE `route_id` = '
						. $rid . ' AND `order` BETWEEN ' . $order . ' AND ' . (CUSTOMER_ADDSEQUENCE - 1);
				if (!db_query($query))
					break;

				// Finally, update the customer's order
				if (!db_query('UPDATE `routes_sequence` SET `order` = ' . $order
						. ' WHERE `tag_id` = ' . $who))
				{
					break;
				}
				$err = ERR_SUCCESS;
			} while (false);

			if ($err >= ERR_SUCCESS)
			{
				// Commit sequence changes
				db_query(SQL_COMMIT);

				// Let user know what they did
				$html = '<center><span>Moved Customer ' . sprintf('%08d', intval($_POST['id']))
						. ' to before Customer ' . sprintf('%08d', $where) . '</span><center><br>';

				audit('Updated route ' . rid2title($rid, true) . ' order. Moved customer '
						. sprintf('%08d', intval($_POST['id'])) . ' to before customer '
						. sprintf('%08d', $where) . '.');

				// Reset variables so we aren't fooled into trying it again
				unset($_POST['action']);
				unset($_POST['id']);
			}
			else
			{
				// Undo the changes
				db_query(SQL_ROLLBACK);
				$html = '<center><span>Unable to move Customer ' . sprintf('%08d', intval($_POST['id']))
						. ' to before Customer ' . sprintf('%08d', intval($action))
						. ' do to error</span><center><br>' . sprintf('$err = %d<br/>$errText = \'%s\'<br/>', $err, $errText);
			}
		}
		else if (isset($_POST['action']) && strlen($_POST['action']) == 7)
		{
			if (preg_match('/^s[0-9]{8}$/', $_POST['action']))
			{
				// Update the note of the customer
				$cid = intval(substr($_POST['action'], 1));
				$temp = 'notes' . sprintf('%08d', $cid);
				$result = db_update('customers', array('id' => $cid),
						array('deliveryNote' => "'" . db_escape(stripslashes($_POST[$temp])) . "'"));
				$html = '<div>';
				if ($result)
				{
					$html .= '<span>Saved Notes For Customer '
							. sprintf('%08d', $cid) . '</span></div><br>';
					audit('Updated customer ' . sprintf('%08d', $cid)
							. '. delieveryNote now is \'' . stripslashes($_POST[$temp]) . '\'.');
				}
				else
				{
					$html .= '<span>Unable To Save Notes For Customer '
							. sprintf('%08d', $cid) . '</span></div><br>';
				}
				unset($_POST['action']);
			}
			else if (preg_match('/^r[0-9]{8}$/', $_POST['action']))
			{
				$resetNote = true;
				$resetNoteId = intval(substr($_POST['action'], 1));
				$html = '<center>Reverted Notes For Customer <span>' . sprintf('%08d', $resetNoteId) . '</span></center><br>';
				unset($_POST['action']);
			}
		}

		// Make sure delivery type info is available
		populate_types();

		// Display list of unsequenced customers first
		$query =
'
SELECT
	`s`.`tag_id`,
	`s`.`route_id`,
	`s`.`order`
FROM
	`routes_sequence` AS `s`
WHERE
	`s`.`route_id` = ' . $rid . '
	AND `s`.`order` = ' . CUSTOMER_ADDSEQUENCE . '
ORDER BY
	`s`.`tag_id` ASC
';
		$records = db_query($query);
		if ($records)
		{
			// Figure out whether to display delivery notes
			if (isset($_POST['notes']) && intval($_POST['notes']) == 1)
				$delNotes = true;
			else
				$delNotes = false;

			// Build title line for main sequencing table
			$odd = false;
			$displayed = 0;
			$tempHtml = '';
			while ($record = $records->fetch_object())
			{
				// Update row setting
				if ($odd)
					$odd = false;
				else
					$odd = true;

				// Display appropraite information
                $display = display_customer($record->tag_id, $odd, true, $delNotes, $resetNote, $resetNoteId);
				if (!empty($display))
					++$displayed;
				$tempHtml .= $display;
			}
			if ($displayed == 0)
			{
				$html .= '<table>'
						. '<tr><td colspan="8">No Unsequenced Customers</td></tr>'
						. '</table><br />';
			}
			else
			{
				$html .= '<table>'
						. '<thead>'
						. '<tr>'
						. '<th>&nbsp;</th>'
						. '<th>&nbsp;</th>'
						. '<th>ID</th>'
						. '<th>Name</th>'
						. '<th>Address</th>'
						. '<th>&nbsp;</th>';
				if ($delNotes)
					$html .= '<th>Notes</th>';
				$html .= '</tr>'
						. '</thead><tbody>'
						. $tempHtml
						. '</tbody></table><br />';
			}
		}

		// Get customers in current order
		$query =
'
SELECT
	`s`.`tag_id`,
	`s`.`route_id`,
	`s`.`order`
FROM
	`routes_sequence` AS `s`
WHERE
	`s`.`route_id` = ' . $rid . '
	AND `s`.`order` < ' . CUSTOMER_ADDSEQUENCE . '
ORDER BY
	`s`.`order` ASC,
	`s`.`tag_id` ASC
';
		$records = db_query($query);
		if ($records)
		{
			// Figure out whether to display delivery notes
			if (isset($_POST['notes']) && intval($_POST['notes']) == 1)
				$delNotes = true;
			else
				$delNotes = false;

			// Build title line for main sequencing table
			$html .= '<table>'
					. '<thead>'
					. '<tr>'
					. '<th>&nbsp;</th>'
					. '<th>&nbsp;</th>'
					. '<th>ID</th>'
					. '<th>Name</th>'
					. '<th>Address</th>'
					. '<th>&nbsp;</th>';
			if ($delNotes)
				$html .= '<th>Notes</th>';
			$html	.= '</tr>'
					. '</thead><tbody>';
			$odd = false;
			while ($record = $records->fetch_object())
			{
				// Update row setting
				if ($odd)
					$odd = false;
				else
					$odd = true;

				// Display appropraite information
                $html .= display_customer($record->tag_id, $odd, false, $delNotes, $resetNote, $resetNoteId);
			}
			$html .= '</tbody></table>';
		}
		else
		{
			$html = '<table>'
					. '<thead><tr>'
					. '<th>0</th>'
					. '<th>K</th>'
					. '<th>ID</th>'
					. '<th>Name</th>'
					. '<th>Address</th>'
					. '<th>&nbsp;</th>'
					. '</tr></thead><tbody><tr>'
					. '<td colspan="5">Error Prevents Display</td>'
					. '</tr></tbody></table>';
		}

		return $html;
	}

