<?php
/*
	Copyright 2005, 2006, 2007, 2008, 2009 Russell E. Gibson

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

	define('PAGE', SCDR_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

	// Make sure we were passed configuration keys
	if (!isset($_REQUEST['id']) || !isset($_REQUEST['s']) || !isset($_REQUEST['e']))
	{
		echo invalid_parameters('View Rate', 'popups/rates/view.php');
		return;
	}

	populate_types();
	populate_periods();

	$CBT = get_config('customer-billing-type');
	if ($CBT == CFG_NONE)
		$CBT = 'auto';
	
	$rate = lup_c_rate($_REQUEST['id'], $_REQUEST['s'], $_REQUEST['e']);

	//-------------------------------------------------------------------------
	$styles = '';

	//-------------------------------------------------------------------------
	$script =
'
';

	//-------------------------------------------------------------------------

	echo gen_htmlHeader('Edit Rate', $styles, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Type</td>
				<td><?php echo $DeliveryTypes[$rate->type_id]['abbr']; ?></td>
			</tr>
			<tr>
				<td>Begin</td>
				<td><?php echo iid2title($rate->period_id_begin); ?></td>
			</tr>
			<tr>
				<td>End</td>
				<td>
<?php
	if (is_null($rate->period_id_end))
		echo 'Current';
	else
		echo iid2title($rate->period_id_end);
?>
				</td>
			</tr>
			<tr>
				<td>Rate</td>
				<td>$<?php printf('%01.2f', $rate->rate); ?></td>
			</tr>
<?php
	if ($CBT != 'auto')
	{
		echo '<tr>'
				. '<td>Daily Credit</td>'
				. '<td>$' . sprintf('%01.4f', $rate->daily_credit) . '</td>'
				. '</tr>'
				. '<tr>'
				. '<td>Sunday Credit</td>'
				. '<td>$' . sprintf('%01.4f', $rate->sunday_credit) . '</td>'
				. '</tr>';
	}
?>
		</table>
	</form>
<?php
	echo gen_htmlFooter();
?>
