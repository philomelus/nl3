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

    define('ROOT', '../../../');

    set_include_path(ROOT . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SCDT_EDIT);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

//=============================================================================

	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['id']) || intval($_REQUEST['id']) == 0)
	{
		echo invalid_parameters('Edit Delivery Type', 'popups/Types/Edit.php');
		return;
	}

//=============================================================================

	$tid = intval($_REQUEST['id']);
	$types = gen_typesArray();
	$message = '';

//=============================================================================

	// If they already modified it and accepted it, update database
	if (isset($_REQUEST['action']))
	{
		// Just executing this code handles the "Update" button
		if ($_REQUEST['action'] == 'Save Changes')
		{
			// Prepare query
			$query = "UPDATE `customers_types` SET";
			$comma = '';
			$audit = array();
			$temp = stripslashes($_REQUEST['name']);
			if ($temp != stripslashes($types[$tid]['name']))
			{
				$query .= $comma . " `name` = '" . db_escape($temp) . "'";
				$comma = ',';
				$audit['name'] = array($types[$tid]['name'], $temp);
			}
			$temp = stripslashes($_REQUEST['abbr']);
			if ($temp != stripslashes($types[$tid]['abbr']))
			{
				$query .= $comma . " `abbr` = '" . db_escape($temp) . "'";
				$comma = ',';
				$audit['abbr'] = array($types[$tid]['abbr'], $temp);
			}
			$val = hexdec(substr($_REQUEST['color'], 1, 6));
			if ($val != $types[$tid]['color'])
			{
				$query .= $comma . " `color` = " . $val;
				$comma = ',';
				$audit['color'] = array(sprintf('#%06X', $types[$tid]['color']), sprintf('#%06X', $val));
			}
			$temp = $_REQUEST['visible'];
			if (($temp == 'Y' && !$types[$tid]['visible'])
					|| ($temp == 'N' && $types[$tid]['visible']))
			{
				$query .= $comma . " `visible` = '" . $temp . "'";
				$comma = ',';
				$audit['visible'] = array($types[$tid]['visible'] ? 'TRUE' : 'FALSE',
						$temp);
			}
			$temp = $_REQUEST['newChange'];
			if (($_REQUEST['newChange'] == 'Y' && !$types[$tid]['newChange'])
					|| ($_REQUEST['newChange'] == 'N' && $types[$tid]['newChange']))
			{
				$query .= $comma . " `newChange` = '" . $_REQUEST['newChange'] . "'";
				$comma = ',';
				$audit['newChange'] = array($types[$tid]['newChange'] ? 'TRUE' : 'FALSE',
						$temp);
			}
			$temp = $_REQUEST['watchStart'];
			if (($_REQUEST['watchStart'] == 'Y' && !$types[$tid]['watchStart'])
					|| ($_REQUEST['watchStart'] == 'N' && $types[$tid]['watchStart']))
			{
				$query .= $comma . " `watchStart` = '" . $_REQUEST['watchStart'] . "'";
				$comma = ',';
				$audit['watchStart'] = array($types[$tid]['watchStart'] ? 'TRUE' : 'FALSE',
						($temp == 'Y' ? 'Y' : 'N'));
			}
			foreach(array
				(
					'su' => 'Sun',
					'mo' => 'Mon',
					'tu' => 'Tue',
					'we' => 'Wed',
					'th' => 'Thu',
					'fr' => 'Fri',
					'sa' => 'Sat'
				) as $key => $key2)
			{
				$temp = $_REQUEST[$key];
				if (($temp == 'Y' && !$types[$tid][$key2]['paper'])
						|| ($temp == '' && $types[$tid][$key2]['paper']))
				{
					$query .= $comma . " `" . $key . "` = '" . ($temp == 'Y' ? 'Y' : 'N') . "'";
					$comma = ',';
					$audit[$key] = array($types[$tid][$key2]['paper'] ? 'TRUE' : 'FALSE',
							($temp == 'Y' ? 'Y' : 'N'));
				}
			}

			// Update record if needed
			if ($comma == ',')
			{
				$query .= " WHERE `id` = " . $tid . " LIMIT 1";
				$result = db_query($query);
				if (!$result)
					$message = '<span>Update failed!</span>';
				else
				{
					audit('Updated type ' . tid2abbr($tid) . '. ' . audit_update($audit));
					$message = '<span>Update successful!</span>';
					$types = gen_typesArray();
				}
			}
		}
	}

//-----------------------------------------------------------------------------
	$styles = '';

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/js_color_picker_v2.js"></script>
<script type="text/javascript">
</script>
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Edit Delivery Type', $styles, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

	// Add message if needed
	if (isset($message) && !empty($message))
	{
?>
		<div><?php echo $message ?></div>
		<br />
<?php
	}
?>
	<script type="text/javascript">pathToImages='../../../img/color_picker/';</script>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Name</td>
				<td>
<?php
	if (isset($_REQUEST['name']) && !empty($_REQUEST['name']))
		$val = stripslashes($_REQUEST['name']);
	else
		$val = $types[$tid]['name'];
?>
					<input type="text" name="name" value="<?php echo $val ?>" size="30" maxlength="30" />
				</td>
			</tr>
			<tr>
				<td>Abbreviation</td>
				<td>
<?php
	if (isset($_REQUEST['abbr']) && !empty($_REQUEST['abbr']))
		$val = stripslashes($_REQUEST['abbr']);
	else
		$val = $types[$tid]['abbr'];
?>
					<input type="text" name="abbr" value="<?php echo $val ?>" size="10" maxlength="10" />
				</td>
			</tr>
			<tr>
				<td>Color</td>
				<td>
<?php
	if (isset($_REQUEST['color']) && !empty($_REQUEST['color']))
		$val = $_REQUEST['color'];
	else
		$val = sprintf('#%06X', $types[$tid]['color']);;
?>
					<input type="text" name="color" value="<?php echo $val ?>" size="7" maxlength="7" />
					<input type="button" value="Choose Color" onclick="showColorPicker(this,document.forms[0].color)" >
					<table>
						<tr>
							<td>
								<input type="submit" name="action" value="Update" />
							</td>
							<td>
								<span>Sample</span>
							</td>
							<td>
								<span>Sample</span>
							</td>
							<td>
								<span>Sample</span>
							</td>
							<td>
								<span>Sample</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>Visible</td>
				<td>
<?php
	if (isset($_REQUEST['visible']))
	{
		if ($_REQUEST['visible'] == 'Y')
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
	}
	else
	{
		if ($types[$tid]['visible'])
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
	}
?>
					<input type="radio" name="visible" value="Y"<?php echo $yes ?>>Yes</input>
					<input type="radio" name="visible" value="N"<?php echo $no ?>>No</input>
					<span>(Type appears in type selection?)</span>
				</td>
			</tr>
			<tr>
				<td>New Change</td>
				<td>
<?php
	if (isset($_REQUEST['newChange']))
	{
		if ($_REQUEST['newChange'] == 'Y')
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
	}
	else
	{
		if ($types[$tid]['newChange'])
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
	}
?>
					<input type="radio" name="newChange" value="Y"<?php echo $yes ?>>Yes</input>
					<input type="radio" name="newChange" value="N"<?php echo $no ?>>No</input>
					<span>(Auto-generate new customer Start change?)</span>
				</td>
			</tr>
			<tr>
				<td>Watch Start</td>
				<td>
<?php
	if (isset($_REQUEST['watchStart']))
	{
		if ($_REQUEST['watchStart'] == 'Y')
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
	}
	else
	{
		if ($types[$tid]['watchStart'])
		{
			$yes = ' checked="checked"';
			$no = '';
		}
		else
		{
			$yes = '';
			$no = ' checked="checked"';
		}
	}
?>
					<input type="radio" name="watchStart" value="Y"<?php echo $yes ?>>Yes</input>
					<input type="radio" name="watchStart" value="N"<?php echo $no ?>>No</input>
					<span>(Affect order prodution?)</span>
				</td>
			</tr>
			<tr>
				<td>Delivery</td>
				<td>
					<table>
						<tr>
							<th>Su</th>
							<th>Mo</th>
							<th>Tu</th>
							<th>We</th>
							<th>Th</th>
							<th>Fr</th>
							<th>Sa</th>
						</tr>
						<tr>
<?php
	$val = array
		(
			'su' => '',
			'mo' => '',
			'tu' => '',
			'we' => '',
			'th' => '',
			'fr' => '',
			'sa' => ''
		);
	foreach(array
		(
			'su' => 'Sun',
			'mo' => 'Mon',
			'tu' => 'Tue',
			'we' => 'Wed',
			'th' => 'Thu',
			'fr' => 'Fri',
			'sa' => 'Sat'
		) as $key => $key2)
	{
		if (isset($_REQUEST['firsttime']))
		{
			if ($_REQUEST[$key] == 'Y')
				$val[$key] = ' checked="checked"';
		}
		else
		{
			if ($types[$tid][$key2]['paper'])
				$val[$key] = ' checked="checked"';
		}
	}
?>
							<td>
								<input type="checkbox" name="su" value="Y"<?php echo $val['su'] ?> />
							</td>
							<td>
								<input type="checkbox" name="mo" value="Y"<?php echo $val['mo'] ?> />
							</td>
							<td>
								<input type="checkbox" name="tu" value="Y"<?php echo $val['tu'] ?> />
							</td>
							<td>
								<input type="checkbox" name="we" value="Y"<?php echo $val['we'] ?> />
							</td>
							<td>
								<input type="checkbox" name="th" value="Y"<?php echo $val['th'] ?> />
							</td>
							<td>
								<input type="checkbox" name="fr" value="Y"<?php echo $val['fr'] ?> />
							</td>
							<td>
								<input type="checkbox" name="sa" value="Y"<?php echo $val['sa'] ?> />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="action" value="Save Changes" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
		<input type="hidden" name="firsttime" value="false" />
	</form>
<?php
	echo gen_htmlFooter();
?>
