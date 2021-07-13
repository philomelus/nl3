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

	require_once 'inc/security.inc.php';

	define('PAGE', S_STORES);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/menu.inc.php';
	require_once 'inc/form.inc.php';

    require 'vendor/autoload.php';

	//-------------------------------------------------------------------------

	function display()
	{
		global $smarty;
		global $err, $errCode, $errContext, $errQuery, $errText;
        global $errorList;
        global $message;
        global $resultHtml;

        // Generate array of +/- 5 weeks from current week
        $cur = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $year = date('Y', $cur);
        $week = date('W', $cur);
        $current = $week - 1;
        $result = '';
        $options = array();
        for ($w = $week - 5; $w < $week + 5; ++$w)
        {
            $startOfWeek = date_isodate_set(new DateTime(), $year, $w, 0);
            $endOfWeek = date_isodate_set(new DateTime(), $year, $w, 6);
            $value = $w + 5 - $week;
            $selected = '';
            if ($w == $current)
            {
                $selected = ' selected="selected"';
                $selectedTimestamp = $startOfWeek->getTimestamp();
            }
            $content = $startOfWeek->format('m/d/Y') . ' - ' . $endOfWeek->format('m/d/Y');
            $options[$w] = '<option value="' . $value . '"' . $selected . '>' . $content . '</option>';
        }
        $smarty->assign('selectedTimestamp', $selectedTimestamp);
        $smarty->assign('options', $options);

        if (!isset($errorList))
            $errorList = array();
        $smarty->assign('errorList', $errorList);
        if (!isset($messsage))
            $message = '';
        $smarty->assign('message', $errorList);

        // Generate array of dates for week
        // TODO:  First time in, this is correct ... this forces it to rely on
        //        the javascript to make it correct post initial display ...
        //        good?  bad?  ugly?  Dunno...
        $year = date('Y');
        $week = date('W') - 1;
        $days = array();
        for ($i = 0; $i < 7; ++$i)
        {
            $day = date_isodate_set(new DateTime(), $year, $week, $i);
            $days[$i] = $day->format('m/d');
        }
        $smarty->assign("days", $days);

        // Display page
        $smarty->display("stores.tpl");
	}

	//-------------------------------------------------------------------------

	function submit()
	{
        global $message;
        global $resultHtml;
        global $err, $errCode, $errContext, $errQuery, $errText;
        global $message;

        // (Simple) Hacks aren't allowed
        if (!isset($_POST['action']) || $_POST['action'] != 'Send')
        {
            $message = '<div>Unknown request received!</div>';
            return '';
        }

        // (Less simple) Hacks aren't allowed
        if (!isset($_POST['sow']) || !preg_match('/^[[:digit:]]+$/', $_POST['sow']))
        {
            $message = '<div>Date transmission failed!</div>';
            return '';
        }

        // Read spreadsheet
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        $spreadsheet = $reader->load(__DIR__ . '/stores/returns.xls');
        $sheet = $spreadsheet->getActiveSheet();

        // Day to cell column translation
        define('cD2C', array(0 => 'B', 1 => 'C', 2 => 'D', 3 => 'E',
                             4 => 'F', 5 => 'G', 6 => 'H'));
        define('cWE', 'B4');    // Week Ending Cell
        define('cDDL', 7);      // draw date line
        define('cDL',  8);      // draw lines
        define('cDTL', 14);     // draw total line
        define('cRDL', 17);     // return date line
        define('cRL',  18);     // return liines
        define('cRTL', 24);     // return total line

        // Update fields
        $startOfWeek = new DateTime();
        $startOfWeek->setTimestamp(intval($_POST['sow']));
        $oneday = new DateInterval('P1D');
        for ($dayIndex = 0; $dayIndex < 7; $dayIndex++)
        {
            $dayCol = cD2C[$dayIndex];

            // Update dates
            $day = new DateTime($startOfWeek->format('H:i:s Y-m-d'));
            for ($x = $dayIndex; $x > 0; $x--)
                $day->add($oneday);
            //$day = date_isodate_set(new DateTime(), $year, $week, $dayIndex);
            $val = $day->format('n-d-y');
            $sheet->setCellValue($dayCol . cDDL, $val);
            $sheet->setCellValue($dayCol . cRDL, $val);

            // Update quantities and calculate totals
            $drawTotal = 0;
            $returnTotal = 0;

            for ($routeIndex = 0; $routeIndex < 4; ++$routeIndex)
            {
                // For draw
                $val = intval($_POST['d'][$dayIndex][$routeIndex]);
                $sheet->setCellValue($dayCol . (cDL + $routeIndex), $val);
                $drawTotal += $val;

                // For returns
                $val = intval($_POST['r'][$dayIndex][$routeIndex]);
                $sheet->setCellValue($dayCol . (cRL + $routeIndex), $val);
                $returnTotal += $val;
            }

            // Update totals
            $sheet->setCellValue($dayCol . cDTL, $drawTotal);
            $sheet->setCellValue($dayCol . cRTL, $returnTotal);

            // When loop finishes this will be the date of the
            // last day in the week (Saturday)
            $endOfWeek = $day;
        }

        // Set the end of week date
        $sheet->setCellValue(cWE, $day->format('Y-m-d'));     

        // Save spreadsheet
        $filename = '/tmp/gearhart-returns-' . $endOfWeek->format('m-d-Y') . '.xls';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
        $writer->save($filename);

        // Send email
        $mime = new Mail_mime();
        $mime->setTXTBody('Gearhart Oregonian returns for week ending '
                          . $endOfWeek->format('Y-m-d') . ' attached.');
        $mime->addAttachment($filename);

        $receiver = get_config('returns-receiver', 'russg@rnstech.com');
        $mail =& Mail::factory('mail');
        $mail->send($receiver,
                    $mime->headers(array('From' => 'russg@rnstech.com',
                                         'Subject' => 'Gearhart Returns '
                                                 . $endOfWeek->format('Y-m-d'),
                                         'CC' => 'ddisciullo@advancelocal.com',
                                         'CC' => 'bmaly@advancelocal.com',
                                         'CC' => 'singlecopy@oregonian.com')),
                    $mime->get());

        $message = '<div>Email Sent Successfully.</div>';

        return '';
	}

	$MENUS->display_page(IDM_STORES);
?>
