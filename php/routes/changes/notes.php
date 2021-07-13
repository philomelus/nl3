<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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

    require_once('inc/sql.inc.php');
    
	//-------------------------------------------------------------------------

	function subdisplay()
	{
		global $Routes, $smarty;

		populate_routes();

		$smarty->display("menu.tpl");

?>
		<table>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Save Notes" />
				</td>
			</tr>
			<tr>
				<td>
					<label>Date</label>
				</td>
				<td>
<?php
		if (isset($_REQUEST['changesm']) && isset($_REQUEST['changesd']) && isset($_REQUEST['changesy']))
			$date = strtotime(valid_date('changes', 'Date'));
		else
		{
			$date = strtotime('+1 day', time());
			$query = "SELECT * FROM `routes_changes_notes` WHERE `date` = '" . strftime('%Y-%m-%d', $date) . "'";
			$result = db_query($query);
			if ($result)
			{
				while ($note = $result->fetch_object())
				{
					if (is_null($note->route_id))
						$_REQUEST['notes'] = $note->note;
					else
						$_REQUEST['notes' . $note->route_id] = $note->note;
				}
			}
		}
		$_REQUEST['changesm'] = date('n', $date);
		$_REQUEST['changesd'] = date('j', $date);
		$_REQUEST['changesy'] = date('Y', $date);
		echo gen_dateField('changes');
?>
					<input type="submit" name="action" value="Refresh" />
				</td>
			</tr>
<?php
?>
			<tr>
				<td>
					Message for<br />All Drivers
				</td>
				<td colspan="2">
<?php
		if (isset($_REQUEST['notes']))
			$temp = stripslashes($_REQUEST['notes']);
		else
			$temp = '';
?>
					<textarea name="notes" rows="3" cols="60"><?php echo $temp ?></textarea>
				</td>
			</tr>
<?php
		// Add route specific notes
		reset($Routes);
		foreach($Routes as $rid => $name)
		{
				// Lookup note for the date for this route if needed
				$index = 'notes' . $rid;

?>
			<tr>
				<td>
					Message for<br />Route <?php echo $name ?>
				</td>
				<td colspan="2">
<?php
		if (isset($_REQUEST[$index]))
			$temp = stripslashes($_REQUEST[$index]);
		else
			$temp = '';
?>
					<textarea name="<?php echo $index ?>" rows="3" cols="60"><?php echo $temp ?></textarea>
				</td>
			</tr>
<?php
		}
?>
		</table>
<?php
	}

	//-------------------------------------------------------------------------

	function subsubmit()
	{
		global $DB;
		global $Routes;
		global $DeliveryTypes;
		global $err, $errCode, $errContext, $errQuery, $errText;

		populate_routes();
		populate_types();

		// Determine date for report(s)
		$datetext = valid_date('changes', 'Date');
		if ($err < ERR_SUCCESS)
			return '';
		$date = strtotime($datetext);

		if ($_REQUEST['action'] == 'Refresh')
		{
			$query = "SELECT * FROM `routes_changes_notes` WHERE `date` = '" . strftime('%Y-%m-%d', $date) . "'";
			$result = db_query($query);
			if ($result)
			{
				$_REQUEST['notes'] = '';
				reset($Routes);
				foreach($Routes as $rid => $route)
					$_REQUEST['notes' . $rid] = '';
				while ($note = $result->fetch_object())
				{
					if (is_null($note->route_id))
						$_REQUEST['notes'] = $note->note;
					else
						$_REQUEST['notes' . $note->route_id] = $note->note;
				}
			}
		}
		else if ($_REQUEST['action'] == 'Save Notes')
		{
            // Encase in transaction
            db_query(SQL_TRANSACTION);
            if ($err < ERR_SUCCESS)
                return '';

            do
            {
                // Map route_id => html field name
                $routes = array('NULL' => 'notes');
                reset($Routes);
                foreach($Routes as $id => $route)
                    $routes[$id] = 'notes' . $id;

                // Insert or update each note
                foreach ($routes as $val => $ui)
                {
                    // Insert or update?
                    $query = 'SELECT `id`, `note` FROM `routes_changes_notes` WHERE `date` = \''
                            . strftime('%Y-%m-%d', $date) . '\' AND `route_id` <=> '
                            . $val . ' LIMIT 1';
                    $result = db_query($query);
                    if (!$result)
                        break;
                    list($id, $note) = $result->fetch_row();
                    if (is_null($id))
                    {
                        // Insert note
                        if (!db_insert('routes_changes_notes',
                                       array ('date' => "'" . strftime('%Y-%m-%d', $date) . "'",
                                              'route_id' => $val,
                                              'created' => 'NOW()',
                                              'note' => "'" . db_escape(stripslashes($_POST[$ui])) . "'")))
                        {
                            break;
                        }
                    }
                    else
                    {
                        // Update note if needed
                        $newnote = stripslashes($_POST[$ui]);
                        if (stripslashes($note) != $newnote)
                        {
                            if (!db_update('routes_changes_notes',
                                           array ('id' => $id),
                                           array ('note' => "'" . db_escape($newnote) . "'")))
                            {
                                break;
                            }
                        }
                    }
                }
            } while (false);

            if ($err >= ERR_SUCCESS)
            {
                db_query(SQL_COMMIT);
                audit('Updated route change notes for ' . strftime('%Y-%m-%d', $date) . '.');
            }
            else
                db_query(SQL_ROLLBACK);
		}

		return '';
	}

?>
