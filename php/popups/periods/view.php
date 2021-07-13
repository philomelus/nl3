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

	set_include_path('../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SAP_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/form.inc.php';

	if (!isset($_REQUEST['id']))
	{
		echo invalid_parameters('View Period', 'popups/periods/view.php');
		return;
	}

	populate_periods();

	$IID = intval($_REQUEST['id']);

	$script =
'
<script type="text/javascript" src="../../js/calendar.js"></script>
';

	$style = '';

//-----------------------------------------------------------------------------

	echo gen_htmlHeader('Edit Period', $style, $script);

	// Generate error info if needed
	if ($err < 0)
		echo gen_error(true, true);

	// Add message if available
	if (isset($message) && !empty($message))
	{
?>
		<div><?php echo $message ?></div>
<?php
	}
?>
	<script type="text/javascript">pathToImages='../../img/';</script>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tr>
				<td>Title</td>
				<td><?php echo $Periods[$IID]['title'] ?></td>
			</tr>
			<tr>
				<td>Changes Start</td>
				<td><?php echo strftime('%m/%d/%Y', $Periods[$IID][P_START]) ?></td>
			</tr>
			<tr>
				<td>Changes End</td>
				<td><?php echo strftime('%m/%d/%Y', $Periods[$IID][P_END]) ?></td>
			</tr>
			<tr>
				<td>Bill</td>
				<td><?php echo strftime('%m/%d/%Y', $Periods[$IID][P_BILL]) ?></td>
			</tr>
			<tr>
				<td>Display Start</td>
				<td><?php echo strftime('%m/%d/%Y', $Periods[$IID][P_DSTART]) ?></td>
			</tr>
			<tr>
				<td>Display End</td>
				<td><?php echo strftime('%m/%d/%Y', $Periods[$IID][P_DEND]) ?></td>
			</tr>
			<tr>
				<td>Due Date</td>
				<td><?php echo strftime('%m/%d/%Y', $Periods[$IID][P_DUE]) ?></td>
			</tr>
		</table>
	</form>
<?php
	echo gen_htmlFooter();
?>
