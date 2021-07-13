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

	define('PAGE', SCDT_ADD);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

	$message = '';

//=============================================================================

	// If they already modified it and accepted it, update database
	if (isset($_REQUEST['action']))
	{
		// Just executing this code handles the "Update" button
		if ($_REQUEST['action'] == 'Add')
		{
			// Prepare field list
			$fields = array();
			$fields['name'] = "'" . db_escape(stripslashes($_REQUEST['name'])) . "'";
			$fields['abbr'] = "'" . db_escape(stripslashes($_REQUEST['abbr'])) . "'";
			$fields['color'] = hexdec(substr($_REQUEST['color'], 1, 6));
			$fields['visible'] = "'" . $_REQUEST['visible'] . "'";
			$fields['newChange'] = "'" . $_REQUEST['newChange'] . "'";
			$fields['watchStart'] = "'" . $_REQUEST['watchStart'] . "'";
			foreach(array('su', 'mo', 'tu', 'we', 'th', 'fr', 'sa') as $key)
			{
				if (isset($_REQUEST[$key]) && $_REQUEST[$key] == 'Y')
					$fields[$key] = "'Y'";
				else
					$fields[$key] = "'N'";
			}

			// Add record
			$result = db_insert('customers_types', $fields);
			if (!$result)
				$message = '<span>Type add failed!</span>';
			else
			{
				audit('Added type ' . $fields['abbr'] . ' (id = ' . sprintf('%04d', $result) . '). '
						. audit_add($fields));
				$message = '<span>Type added successfully as ' . gen_c_typeid($result) . '!</span>';
			}
		}
	}

//-----------------------------------------------------------------------------
	$styles = '';

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript" src="../../../js/js_color_picker_v2.js"></script>
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Add Delivery Type', $styles, $script);

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
		$val = '';
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
		$val = '';
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
		$val = '#000000';
?>
					<input type="input" name="color" value="<?php echo $val ?>" size="8" maxlength="8" />
					<input type="button" value="Choose Color" onclick="showColorPicker(this,document.forms[0].color)" />
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
		$yes = ' checked="checked"';
		$no = '';
	}
?>
					<input type="radio" name="visible" value="Y"<?php echo $yes ?>>Yes</input>
					<input type="radio" name="visible" value="N"<?php echo $no ?>>No</input>
					<span>(Compute bill by charging for individual papers?)</span>
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
		$yes = ' checked="checked"';
		$no = '';
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
		$yes = ' checked="checked"';
		$no = '';
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
	if (isset($_REQUEST['su']) && $_REQUEST['su'] == 'Y')
		$val['su'] = ' checked="checked"';
	if (isset($_REQUEST['mo']) && $_REQUEST['mo'] == 'Y')
		$val['mo'] = ' checked="checked"';
	if (isset($_REQUEST['tu']) && $_REQUEST['tu'] == 'Y')
		$val['tu'] = ' checked="checked"';
	if (isset($_REQUEST['we']) && $_REQUEST['we'] == 'Y')
		$val['we'] = ' checked="checked"';
	if (isset($_REQUEST['th']) && $_REQUEST['th'] == 'Y')
		$val['th'] = ' checked="checked"';
	if (isset($_REQUEST['fr']) && $_REQUEST['fr'] == 'Y')
		$val['fr'] = ' checked="checked"';
	if (isset($_REQUEST['sa']) && $_REQUEST['sa'] == 'Y')
		$val['sa'] = ' checked="checked"';
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
					<input type="submit" name="action" value="Add" />
				</td>
			</tr>
		</table>
<?php
	if (isset($_REQUEST['tid']))
		$val = intval($_REQUEST['tid']);
	else
		$val = 0;
?>
		<input type="hidden" name="tid" value="<?php echo $val ?>" />
	</form>
<?php
	echo gen_htmlFooter();
?>
