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
	// Handle edit customer notes page display
	function display()
	{
		global $customer;
		global $Periods;
		global $Period;

?>
			<table>
				<tr>
					<td>Notes</td>
					<td>
<?php
		if (isset($_REQUEST['notes']))
			$val = htmlspecialchars(stripslashes($_REQUEST['notes']));
		else
			$val = htmlspecialchars(stripslashes($customer->notes));
?>
						<textarea name="notes" rows="4" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td>Billing Note</td>
					<td>
						Period
						<select onchange="ShowHideControls()">
							<option value="<?php printf('I%04d', $Period[P_PERIOD]); ?>">Current</option>
<?php
		$notes = array();
		$query = "SELECT `iid`,`nt1`, `nt2`, `nt3`, `nt4` FROM `customers_bills` WHERE `cid` = '" . sprintf('%06d', $customer->id) . "' AND "
				. "(`nt1` != '' OR `nt2` != '' OR `nt3` != '' OR `nt4` != '') ORDER BY `iid` DESC";
		$periods = db_query($query);
		if ($periods)
		{
			populate_periods();
			while ($note = $periods->fetch_object())
			{
				$notes[$note->iid] = array
					(
						1 => $note->nt1,
						2 => $note->nt2,
						3 => $note->nt3,
						4 => $note->nt4
					);
				echo '<option value="' . sprintf('I%04d', $note->iid) . '">' . $Periods[$note->iid][P_TITLE] . '</option>';
			}
		}
?>
						</select>
						<br />
<?php
		foreach($notes as $iid => $note)
		{
			echo '<table>'
					. '<tr><td>Line 1</td><td>' . htmlspecialchars($note[1]) . '</td></tr>'
					. '<tr><td>Line 2</td><td>' . htmlspecialchars($note[2]) . '</td></tr>'
					. '<tr><td>Line 3</td><td>' . htmlspecialchars($note[3]) . '</td></tr>'
					. '<tr><td>Line 4</td><td>' . htmlspecialchars($note[4]) . '</td></tr>'
					. '</table>';
		}
?>
						<table>
<?php
		$billNote = array('', '', '', '');
		if (isset($_REQUEST['billNote1']))
		{
			$billNote[0] = stripslashes($_REQUEST['billNote1']);
			$billNote[1] = stripslashes($_REQUEST['billNote2']);
			$billNote[2] = stripslashes($_REQUEST['billNote3']);
			$billNote[3] = stripslashes($_REQUEST['billNote4']);
		}
		else if (!is_null($customer->billNote))
		{
			$temp = explode("\r\n", $customer->billNote);
			if (!empty($temp))
			{
				if (count($temp) >= 1)
					$billNote[0] = $temp[0];
				if (count($temp) >= 2)
					$billNote[1] = $temp[1];
				if (count($temp) >= 3)
					$billNote[2] = $temp[2];
				if (count($temp) >= 4)
					$billNote[3] = $temp[3];
			}
		}
?>
							<tr><td>Line 1</td><td><input type="text" size="32" maxLength="32" name="billNote1" value="<?php echo $billNote[0] ?>" /></td></tr>
							<tr><td>Line 2</td><td><input type="text" size="32" maxLength="32" name="billNote2" value="<?php echo $billNote[1] ?>" /></td></tr>
							<tr><td>Line 3</td><td><input type="text" size="32" maxLength="32" name="billNote3" value="<?php echo $billNote[2] ?>" /></td></tr>
							<tr><td>Line 4</td><td><input type="text" size="32" maxLength="32" name="billNote4" value="<?php echo $billNote[3] ?>" /></td></tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>Delivery Note</td>
					<td>
<?php
		if (isset($_REQUEST['deliveryNote']))
			$val = htmlspecialchars(stripslashes($_REQUEST['deliveryNote']));
		else
			$val = htmlspecialchars(stripslashes($customer->deliveryNote));
?>
						<textarea name="deliveryNote" rows="1" cols="40"><?php echo $val ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</table>
<?php
	}

	//-------------------------------------------------------------------------
	// Return edit customer notes page specific scripts
	function scripts()
	{
		global $DB;
		global $customer;
		global $Period;

		$notes = array();
		$query = "SELECT `iid`,`nt1`, `nt2`, `nt3`, `nt4` FROM `customers_bills` WHERE `cid` = '" . sprintf('%06d', $customer->id) . "' AND "
				. "(`nt1` != '' OR `nt2` != '' OR `nt3` != '' OR `nt4` != '') ORDER BY `iid` DESC";
		$periods = db_query($query);
		if ($periods)
		{
			while ($note = $periods->fetch_object())
			{
				$notes[$note->iid] = array
					(
						1 => $note->nt1,
						2 => $note->nt2,
						3 => $note->nt3,
						4 => $note->nt4
					);
			}
		}

		$ids = '';
		$c = '';
		foreach($notes as $iid => $note)
		{
			$ids .= $c . '"' . sprintf('I%04d', $iid) . '"';
			$c = ',';
		}
		$ids .= $c . '"' . sprintf('I%04d', $Period[P_PERIOD]) . '"';

		$script =
'
<script language="JavaScript">
	function ShowHideControls()
	{
		var ids = new Array(' . $ids . ');
		var when = document.getElementById("when");
		if (when)
		{
			var val = when.options[when.selectedIndex].value;
			for(var i = 0; i < ids.length; ++i)
			{
				var c = document.getElementById(ids[i]);
				if (c)
				{
					if (val == ids[i])
						c.style.display = "";
					else
						c.style.display = "none";
				}
			}
		}
	}
</script>
';

		return $script;
	}

	//-------------------------------------------------------------------------
	// Return edit customer notes page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle edit customer notes page submits
	function submit()
	{
		global $customer, $DB, $errorList, $message, $resultHtml;
		global $err, $errCode, $errContext, $errQuery, $errText;

		if ($_REQUEST['action'] != 'Save Changes')
			return;

		$fields = array();
		$audit = array();

		// Note
		$temp = stripslashes($_POST['notes']);
		if ($temp != stripslashes($customer->notes))
		{
			$fields['notes'] = "'" . db_escape($temp) . "'";
			$audit['notes'] = array(stripslashes($customer->notes), $temp);
		}

		// Billing Note
		$temp = '';
		if (!empty($_POST['billNote1']) || !empty($_POST['billNote2']) || !empty($_POST['billNote3'])
				|| !empty($_POST['billNote4']))
		{
			$temp = stripslashes($_POST['billNote1']) . "\r\n"
					. stripslashes($_POST['billNote2']) . "\r\n"
					. stripslashes($_POST['billNote3']) . "\r\n"
					. stripslashes($_POST['billNote4']);
		}
		if ($temp != stripslashes($customer->billNote))
		{
			$fields['billNote'] = "'" . db_escape($temp) . "'";
			$audit['billNote'] = array(stripslashes($customer->billNote), $temp);
		}

		// Delivery Note
		$temp = stripslashes($_POST['deliveryNote']);
		if ($temp != stripslashes($customer->deliveryNote))
		{
			$fields['deliveryNote'] = "'" . db_escape($temp) . "'";
			$audit['deliveryNote'] = array(stripslashes($customer->deliveryNote), $temp);
		}

		// If anything needs updating, update now
		if (count($fields))
		{
			$result = db_update('customers', array('id' => $customer->id), $fields);
			if ($err >= ERR_SUCCESS)
			{
				$message = '<span>Note(s) updated successfully!</span>';
				audit('Updated customer ' . sprintf('%06d', $customer->id) . '. ' . audit_update($audit));
			}
			else
				$message = '<span>Unable to save changes due to error!</span>';
		}
		else
			$message = '<span>No changes required saving</span>';
	}

?>
