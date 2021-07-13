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

	$message = '';

	function subsubdisplay()
	{
        global $smarty;
		global $resultHtml, $message;
		global $Period;
		global $err;

        // If the length of the note fields are changed, this also needs
        // to be changed...
		if (!isset($_POST['line1']))
		{
			$note = get_globalConfig('billing-note', '');
			$temp = wordwrap($note, 36, "\r\n", true);
			$lines = explode("\r\n", $temp);
			while (count($lines) < 4)
				$lines[] = '';
            foreach($lines as $line)
            {
                if (strlen($line) > 33)
                {
                    $message = '<span>Check note:  Line has been truncated</span>';
                    break;
                }
            }
			$_POST['line1'] = substr($lines[0], 0, 33);
			$_POST['line2'] = substr($lines[1], 0, 33);
			$_POST['line3'] = substr($lines[2], 0, 33);
			$_POST['line4'] = substr($lines[3], 0, 33);
		}

		if ($err < ERR_SUCCESS)
			echo gen_error();

		$state = intval(get_globalConfig('billing-status', BILL_COMPLETE));
		switch ($state)
		{
		case BILL_PENDING:		$dstate = 'Ready';						break;
		case BILL_RUNNING:		$dstate = 'In Process';					break;
		case BILL_GENERATED:	$dstate = 'Repairing';					break;
		case BILL_COMBINED:		$dstate = 'Combined';					break;
		default:
		case BILL_COMPLETE:		$dstate = 'Complete';					break;
		}
        $smarty->assign('dstate', $dstate);

		if ($state == BILL_PENDING)
			$disabled = '';
		else
            $disabled = ' disabled="disabled"';
        $smarty->assign('pending', $disabled);

        $smarty->assign('action', $_SERVER['PHP_SELF'] . '?menu=4&amp;submenu=1&amp;m3=2');

        $smarty->assign('period', $Period[P_PERIOD]);

		$temp = array
			(
				1 => '',
				2 => '',
				3 => '',
				4 => ''
			);
		if (isset($_POST['line1']) && strlen($_POST['line1']) > 0)
			$temp[1] = stripslashes($_POST['line1']);
		if (isset($_POST['line2']) && strlen($_POST['line2']) > 0)
			$temp[2] = stripslashes($_POST['line2']);
		if (isset($_POST['line3']) && strlen($_POST['line3']) > 0)
			$temp[3] = stripslashes($_POST['line3']);
		if (isset($_POST['line4']) && strlen($_POST['line4']) > 0)
			$temp[4] = stripslashes($_POST['line4']);
        $smarty->assign('line1', $temp[1]);
        $smarty->assign('line2', $temp[1]);
        $smarty->assign('line3', $temp[1]);
        $smarty->assign('line4', $temp[1]);

        $smarty->assign('menu', $_REQUEST['menu']);
        $smarty->assign('submenu', $_REQUEST['submenu']);
        $smarty->assign('m3', $_REQUEST['m3']);

        $smarty->display('customers/billing/bill/step2.tpl');
	}

	function subsubsubmit()
	{
		global $message, $err;

		if ($_POST['action'] == 'Save')
		{
			$note = $_POST['line1'] . "\r\n"
					. $_POST['line2'] . "\r\n"
					. $_POST['line3'] . "\r\n"
					. $_POST['line4'];
			$old = get_globalConfig('billing-note', '');
			set_globalConfig('billing-note', $note);
			audit('Update billing note. Old was \'' . $old . '\', new is \'' . $note . '\'.');
			if ($err < ERR_SUCCESS)
				$message = '<span>Failed to save billing note!</span>';
			else
				$message = '<span>Billing note updated.</span>';
		}
	}
?>
