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

	define('PAGE', SCDT_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['id']) || intval($_REQUEST['id']) == 0)
	{
		echo invalid_parameters('View Delivery Type', 'popups/types/view.php');
		return;
	}

	$tid = intval($_REQUEST['id']);
	$types = gen_typesArray();

//-----------------------------------------------------------------------------
	$styles = '';

//-----------------------------------------------------------------------------
	$script =
'
<script type="text/javascript">
</script>
';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('View Delivery Type', $styles, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Name</td>
				<td><?php echo $types[$tid]['name'] ?></td>
			</tr>
			<tr>
				<td>Abbreviation</td>
				<td><?php echo $types[$tid]['abbr'] ?></td>
			</tr>
			<tr>
				<td>Color</td>
<?php
	$color = sprintf('#%06X', $types[$tid]['color']);
?>
				<td><?php echo $color ?></td>
			</tr>
			<tr>
				<td>Visible</td>
				<td><?php echo $types[$tid]['visible'] ? 'Yes' : 'No' ?></td>
			</tr>
			<tr>
				<td>New Change</td>
				<td><?php echo $types[$tid]['newChange'] ? 'Yes' : 'No' ?></td>
			</tr>
			<tr>
				<td>Watch Start</td>
				<td><?php echo $types[$tid]['watchStart'] ? 'Yes' : 'No' ?></td>
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
			'Sun' => $types[$tid]['Sun']['paper'] ? 'X' : '&nbsp;',
			'Mon' => $types[$tid]['Mon']['paper'] ? 'X' : '&nbsp;',
			'Tue' => $types[$tid]['Tue']['paper'] ? 'X' : '&nbsp;',
			'Wed' => $types[$tid]['Wed']['paper'] ? 'X' : '&nbsp;',
			'Thu' => $types[$tid]['Thu']['paper'] ? 'X' : '&nbsp;',
			'Fri' => $types[$tid]['Fri']['paper'] ? 'X' : '&nbsp;',
			'Sat' => $types[$tid]['Sat']['paper'] ? 'X' : '&nbsp;'
		);
?>
							<td><?php echo $val['Sun'] ?></td>
							<td><?php echo $val['Mon'] ?></td>
							<td><?php echo $val['Tue'] ?></td>
							<td><?php echo $val['Wed'] ?></td>
							<td><?php echo $val['Thu'] ?></td>
							<td><?php echo $val['Fri'] ?></td>
							<td><?php echo $val['Sat'] ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
<?php
	echo gen_htmlFooter();
?>
