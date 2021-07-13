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

	set_include_path('../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SI_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/profiledata.inc.php';

//-----------------------------------------------------------------------------

	$data = ProfileData::create();

	if (isset($_GET['key']))
			$_POST['key'] = $_GET['key'];
	if (!isset($_POST['key']))
	{
		$temp = &$data->keys(true);
		$_POST['key'] = $temp[0];
		unset($temp);
	}

	$PROFILE = $data->lookup($_POST['key']);

	// If they already modified it and accepted it, update database
	$message = '';
	if (isset($_REQUEST['action']))
	{
		if ($_REQUEST['action'] == 'Add')
		{
			$fields = array();
			do
			{
				// Add key
				$fields['key'] = "'" . stripslashes($_POST['key']) . "'";

				// Get value
				switch ($PROFILE[ProfileData::TYPE])
				{
				case CFG_BOOLEAN:
					$fields['value'] = '\'' . $_POST['valBOOLEAN'] . '\'';
					break;

				case CFG_COLOR:
					$fields['value'] = hexdec(substr($_POST['color'], 1));
					break;

				case CFG_ENUM:
					$fields['value'] = intval($_POST['valENUM']);
					break;

				case CFG_FLOAT:
					$fields['value'] = floatval($_POST['valFLOAT']);
					break;

				case CFG_INTEGER:
					$fields['value'] = intval($_POST['valINTEGER']);
					break;

				case CFG_MONEY:
					$fields['value'] = floatval($_POST['valMONEY']);
					break;

				case CFG_PERIOD:
					$fields['value'] = intval($_POST['valIID']);
					break;

				case CFG_ROUTE:
					$fields['value'] = intval($_POST['valRID']);
					break;

				case CFG_STRING:
					$fields['value'] = '\'' . db_escape(stripslashes($_POST['valSTRING'])) . '\'';
					break;

				case CFG_TELEPHONE:
					$temp = valid_telephone('tel', 'Telephone', true);
					if ($err < ERR_SUCCESS)
					{
						$message = '<span>' . $errText . '</span>';
						$err = ERR_SUCCESS;
						break;
					}
					$fields['value'] = '\'' . $temp . '\'';
					break;

				case CFG_TYPE:
					$fields['value'] = intval($_POST['valTID']);
					break;
				}
				if (!empty($message))
					break;

				// Insert the new value (or update the previous... not that I'd tell anybody it's possible...)
				$result = db_insert('configuration', $fields);
				if (!$result)
				{
					$message = '<span>Failed to add setting!</span>';
					break;
				}
				$message = '<span>Added setting successfully.</span>';
				audit('Added configuration setting. ' . audit_add($fields));
			} while (false);
		}
	}
	else
	{
		unset($_REQUEST['valBOOLEAN']);
		unset($_REQUEST['color']);
		unset($_REQUEST['valIID']);
		unset($_REQUEST['valENUM']);
		unset($_REQUEST['valFLOAT']);
		unset($_REQUEST['valMONEY']);
		unset($_REQUEST['valRID']);
		unset($_REQUEST['valTID']);
		unset($_REQUEST['valINTEGER']);
		unset($_REQUEST['valSTRING']);
	}

	if (isset($_POST['key1']))
	{
		$keys = array
			(
				2 => '',
				3 => ''
			);
		if ($_POST['key1'] == "All")
		{
			$_POST['key2'] = 'All';
			$keys[2] = ' disabled="disabled"';
			$_POST['key3'] = 'All';
			$keys[3] = ' disabled="disabled"';
		}
		else
		{
			if ($_POST['key2'] == "All")
			{
				$_POST['key3'] = 'All';
				$keys[3] = ' disabled="disabled"';
			}
		}
	}
	else
	{
		$keys = array
			(
				2 => ' disabled="disabled"',
				3 => ' disabled="disabled"'
			);
	}

//-----------------------------------------------------------------------------
	$styles = '';

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../js/js_color_picker_v2.js"></script>
<script type="text/javascript">
	function update_keys()
	{
		var key1 = document.getElementById("key1");
		var key2 = document.getElementById("key2");
		var key3 = document.getElementById("key3");
		if (key1 && key2 && key3)
		{
			if (key1.value == "All")
			{
				key2.value = "All";
				key2.disabled = true;
				key3.value = "All";
				key3.disabled = true;
			}
			else
			{
				key2.disabled = false;
				if (key2.value == "All")
				{
					key3.value = "All";
					key3.disabled = true;
				}
			}
		}
	}
</script>
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Add Configuration', $styles, $script);

	// Generate error info if needed
	if ($err < ERR_SUCCESS)
		echo gen_error(true, true);

	// Add message if needed
	if (isset($message) && !empty($message))
		echo '<div>' . $message . '</div>';

    if (!isset($_POST['key1']))
        $_POST['key1'] = 'All';
?>
	<script type="text/javascript">pathToImages='../../img/color_picker/';</script>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Filter</td>
				<td>
					<select name="key1" onchange="JavaScript:update_keys(); this.form.submit();">
						<option<?php if ($_POST['key1'] == 'All') echo ' selected="selected"'; ?>>All</option>
<?php
    if (!isset($_POST['key2']))
        $_POST['key2'] = 'All';
	$temp = &$data->keys1(true);
	foreach($temp as $key)
	{
		if ($_POST['key1'] == $key)
			$selected = ' selected="selected"';
		else
			$selected = '';
		echo '<option ' . $selected . '>' . $key . '</option>';
	}
	unset($temp);
?>
					</select>
					<select name="key2" onchange="JavaScript:update_keys(); this.form.submit();"<?php echo $keys[2]; ?>>
						<option<?php if ($_POST['key2'] == 'All') echo ' selected="selected"'; ?>>All</option>
<?php
    if (!isset($_POST['key3']))
        $_POST['key3'] = 'All';
	$temp = &$data->keys2(true);
	foreach($temp as $key)
	{
		if ($_POST['key2'] == $key)
			$selected = ' selected="selected"';
		else
			$selected = '';
		echo '<option ' . $selected . '>' . $key . '</option>';
	}
	unset($temp);
?>
					</select>
					<select name="key3" onchange="JavaScript:update_keys(); this.form.submit();"<?php echo $keys[3] ?>>
						<option<?php if ($_POST['key3'] == 'All') echo ' selected="selected"'; ?>>All</option>
<?php
	$temp = &$data->keys3(true);
	foreach($temp as $key)
	{
		if ($_POST['key3'] == $key)
			$selected = ' selected="selected"';
		else
			$selected = '';
		echo '<option ' . $selected . '>' . $key . '</option>';
	}
	unset($temp);
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Key</td>
				<td>
					<select name="key" onchange="this.form.submit();">
<?php
	$filter = '';
	if (!empty($_POST['key1']) && $_POST['key1'] != 'All')
	{
		$filter = $_POST['key1'];
		if (!empty($_POST['key2']) && $_POST['key2'] != 'All')
		{
			$filter .= '-' . $_POST['key2'];
			if (!empty($_POST['key3']) && $_POST['key3'] != 'All')
				$filter .= '-' . $_POST['key3'];
		}
	}
	$temp = &$data->keys(true);
	foreach($temp as $key)
	{
		if (!empty($filter) && strncmp($filter, $key, strlen($filter)))
			continue;
		if ($_POST['key'] == $key)
			$selected = ' selected="selected"';
		else
			$selected = '';
		echo '<option' . $selected . '>' . htmlspecialchars($key) . '</option>';
	}
	unset($temp);
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
<?php
	echo $PROFILE[ProfileData::DESC];
	$canAdd = true;
?>
				</td>
			</tr>
			<tr>
				<td>Type</td>
				<td>
<?php
	$disabled = '';
	switch ($PROFILE[ProfileData::TYPE])
	{
	case CFG_BOOLEAN:	echo 'Boolean'; 			break;
	case CFG_COLOR:		echo 'Color'; 				break;
	case CFG_ENUM:		echo 'Enumerated Integer';	break;
	case CFG_FLOAT:		echo 'Float';				break;
	case CFG_INTEGER:	echo 'Integer'; 			break;
	case CFG_MONEY:		echo 'Money';				break;
	case CFG_PERIOD:	echo 'Period'; 				break;
	case CFG_ROUTE:		echo 'Route'; 				break;
	case CFG_STRING:	echo 'String';				break;
	case CFG_TELEPHONE:	echo 'Telephone';			break;
	case CFG_TYPE:		echo 'Delivery Type';		break;
	default: echo '<span>ERROR! - ' . $_REQUEST['type'] . '</span>'; break;
	}
?>
				</td>
			</tr>
			<tr>
				<td>Value</td>
<?php
	switch ($PROFILE[ProfileData::TYPE])
	{
	case CFG_BOOLEAN:
		if (isset($_REQUEST['valBOOLEAN']) && !empty($_REQUEST['valBOOLEAN']))
			$val = stripslashes($_REQUEST['valBOOLEAN']);
		else
			$val = 'true';
		if ($val == 'true')
		{
			$false = '';
			$true = ' checked="checked"';
		}
		else
		{
			$false = ' checked="checked"';
			$true = '';
		}
		echo '<td>'
				. '<input type="radio" name="valBOOLEAN" value="true"' . $true . '>True</input>'
				. '<input type="radio" name="valBOOLEAN" value="false"' . $false . '>False</input>';
		break;

	case CFG_COLOR:
		if (isset($_REQUEST['color']) && !empty($_REQUEST['color']))
			$val = $_REQUEST['color'];
		else
			$val = '#000000';
		echo '<td>'
				. '<input type="input" name="color" value="' . $val . '" size="8" maxlength="8" />'
				. '<input type="button" value="Choose Color" onclick="showColorPicker(this,document.forms[0].color)" />'
				. '<table>'
				. '<tr>'
				. '<td>'
				. '<input type="submit" name="action" value="Update" />'
				. '</td>'
				. '<td>'
				. '<span>Sample</span>'
				. '</td>'
				. '<td>'
				. '<span>Sample</span>'
				. '</td>'
				. '<td>'
				. '<span>Sample</span>'
				. '</td>'
				. '<td>'
				. '<span>Sample</span>'
				. '</td>'
				. '</tr>'
				. '</table>';
		break;

	case CFG_ENUM:
		if (isset($_REQUEST['valENUM']) && !empty($_REQUEST['valENUM']))
			$val = intval($_REQUEST['valENUM']);
		else
			$val = '0';
		echo '<td>'
				. '<select name="valENUM">';
		foreach($PROFILE[ProfileData::ENUM] as $E => $V)
			echo '<option value="' . $E . '">' . htmlspecialchars($V) . '</option>';
		echo '</select>';
		break;

	case CFG_FLOAT:
		if (isset($_REQUEST['valFLOAT']) && !empty($_REQUEST['valFLOAT']))
			$val = intval($_REQUEST['valFLOAT']);
		else
			$val = '0.0';
		echo '<td>'
				. '<input type="text" name="valFLOAT" value="' . $val . '" size="10" maxlength="10" />';
		break;

	case CFG_INTEGER:
		if (isset($_REQUEST['valINTEGER']) && !empty($_REQUEST['valINTEGER']))
			$val = intval($_REQUEST['valINTEGER']);
		else
			$val = '0';
		echo '<td>'
				. '<input type="text" name="valINTEGER" value="' . $val . '" size="10" maxlength="10" />';
		break;

	case CFG_MONEY:
		if (isset($_REQUEST['valFLOAT']) && !empty($_REQUEST['valFLOAT']))
			$val = intval($_REQUEST['valFLOAT']);
		else
			$val = '0.0';
		echo '<td>'
				. '$<input type="text" name="valFLOAT" value="' . $val . '" size="10" maxlength="10" />';
		break;

	case CFG_PERIOD:
		if (isset($_REQUEST['valIID']) && !empty($_REQUEST['valIID']))
			$val = intval($_REQUEST['valIID']);
		else
			$val = 0;
		echo '<td>';
		echo gen_periodsSelect('valIID', $val, false, '', '');
		break;

	case CFG_ROUTE:
		if (isset($_REQUEST['valRID']) && !empty($_REQUEST['valRID']))
			$val = intval($_REQUEST['valRID']);
		else
			$val = 0;
		echo '<td>';
		echo gen_routesSelect('valRID', $val, false, '', '');
		break;

	case CFG_STRING:
		if (isset($_REQUEST['valSTRING']) && !empty($_REQUEST['valSTRING']))
			$val = htmlspecialchars(stripslashes($_REQUEST['valSTRING']));
		else
			$val = '';
		echo '<td>'
				. '<textarea name="valSTRING" cols="40" rows="4">' . $val . '</textarea>';
		break;

	case CFG_TELEPHONE:
		if (isset($_POST['telAreaCode']))
		{
			$temp = array
				(
					1 => $_POST['telAreaCode'],
					2 => $_POST['telPrefix'],
					3 => $_POST['telNumber'],
					5 => $_POST['telExt']
				);
		}
		else
			$temp = array(1=>'', 2=>'', 3=>'', 5=>'');
		echo '<td>'
				. '(&nbsp;'
				. '<input type="text" name="telAreaCode" value="' . $temp[1] . '" maxLength="3" size="3" />'
				. '&nbsp;)&nbsp;'
				. '<input type="text" name="telPrefix" value="' . $temp[2] . '" maxLength="3" size="3" />'
				. '&nbsp;-&nbsp;'
				. '<input type="text" name="telNumber" value="' . $temp[3] . '" maxLength="4" size="4" />'
				. '&nbsp;ext.&nbsp;'
				. '<input type="text" name="telExt" value="' . $temp[5] . '" maxLength="6" size="4" />';
		break;

	case CFG_TYPE:
		if (isset($_REQUEST['valTID']) && !empty($_REQUEST['valTID']))
			$val = intval($_REQUEST['valTID']);
		else
			$val = '';
		echo '<td>';
		echo gen_typeSelect('valTID', $val, false, '', array(), '');
		break;
	}
?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
<?php
	if (!$canAdd)
		$disabled = ' disabled="disabled"';
?>
					<input type="submit" name="action" value="Add" <?php echo $disabled ?> />
				</td>
			</tr>
		</table>
	</form>
<?php
	echo gen_htmlFooter();
?>
