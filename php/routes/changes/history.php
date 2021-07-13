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

	//-------------------------------------------------------------------------

	function subdisplay()
	{
		global $smarty;
        global $resultHtml;
        global $message;

        $smarty->assign('action', $_SERVER['PHP_SELF'] . '?menu=4&amp;submenu=3');
        $smarty->assign('message', $message);

		if (isset($_REQUEST['startm']) && isset($_REQUEST['startd']) && isset($_REQUEST['starty']))
			$date = strtotime(valid_date('start', 'Date'));
		else
			$date = time();
		$smarty->assign('startMonth', date('n', $date));
		$smarty->assign('startDay', date('j', $date));
        $smarty->assign('startYear', date('Y', $date));

		if (isset($_REQUEST['endm']) && isset($_REQUEST['endd']) && isset($_REQUEST['endy']))
			$date = strtotime(valid_date('end', 'Date'));
		else
			$date = time();
        $smarty->assign('endMonth', date('n', $date));
        $smarty->assign('endDay', date('j', $date));
        $smarty->assign('endYear', date('Y', $date));

        if (isset($_REQUEST['rid']))
            $val = intval($_REQUEST['rid']);
        else
            $val = 0;
        $smarty->assign('rid', $val);

    	if (isset($_REQUEST['search']))
	    	$val = $_REQUEST['search'];
    	else
	    	$val = '';
        $smarty->assign('search', $val);
        $smarty->assign('result', $resultHtml);
        $smarty->assign('menu', $_REQUEST['menu']);
        $smarty->assign('submenu', $_REQUEST['submenu']);
        $smarty->display("routes/changes/history.tpl");
	}

	//-------------------------------------------------------------------------

	function subsubmit()
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $Routes;
        global $message;

        $message = 'subsubmit() called';

		// Get date range
		$startDate = strtotime(valid_date('start', 'Date'));
		if ($err < ERR_SUCCESS)
			return '';
		$endDate = strtotime(valid_date('end', 'Date'));
		if ($err < ERR_SUCCESS)
			return '';

		// Build query
		$query = "SELECT * FROM `routes_changes_notes` WHERE `date` BETWEEN '" . strftime('%Y-%m-%d', $startDate)
				. "' AND '" . strftime('%Y-%m-%d', $endDate) ."'";
		if (intval($_REQUEST['rid']) > 0)
			$query .= " AND `route_id` = " . intval($_REQUEST['rid']);
		if (!empty($_REQUEST['search']))
		{
			$strings = explode(' ', stripslashes($_REQUEST['search']));
			if (count($strings) > 0)
			{
				$query .= " AND (";
				$or = '';
				foreach($strings as $str)
				{
					$query .= $or . "`note` LIKE '%" . db_escape($str) . "%'";
					$or = ' OR ';
				}
				$query .= ")";
			}
		}
		$query .= " ORDER BY `date`, " . db_route_field() . ", `updated`";

		$notes = db_query($query);
		if (!$notes)
			return '';

		$html = '<div>Changes Note History</div>'
				. '<div>';
		if (intval($_REQUEST['rid']) > 0)
			$html .= '<p>Route ' . $Routes[intval($_REQUEST['rid'])] . '</p>';
		$html .= '<p>' . strftime('%m-%d-%Y', $startDate);
		if ($startDate != $endDate)
			$html .= ' - ' . strftime('%m-%d-%Y', $endDate);
		$html .= '</p>';
		if (!empty($_REQUEST['search']))
		{
			if (count($strings) > 0)
			{
				$html .= '<p>Keywords:';
				foreach($strings as $str)
					$html .= ' &quot;' . $str . '&quot;';
				$html .= '</p>';
			}
		}
		$html .= '</div>'
				. '<table>'
				. '<thead>'
				. '<tr>'
				. '<th>Date</th>'
				. '<th>Rt</th>'
				. '<th>Created</th>'
				. '<th>Updated</th>'
				. '<th>Note</th>'
				. '</tr>'
				. '</thead>'
				. '<tbody>';
		if ($notes->num_rows > 0)
		{
			while ($note = $notes->fetch_object())
			{
				if (!empty($note->note))
				{
					$html .= '<tr>'
							. '<td>' . strftime('%m-%d-%Y', strtotime($note->date)) . '</td>'
							. '<td>';
					if (is_null($note->route_id))
						$html .= 'All';
					else
						$html .= $Routes[$note->route_id];
					$html .= '</td>'
							. '<td>' . strftime('%m-%d-%Y %H:%M:%S', strtotime($note->created)) . '</td>'
							. '<td>' . strftime('%m-%d-%Y %H:%M:%S', strtotime($note->updated)) . '</td>'
							. '<td>' . valid_text(stripslashes($note->note)) . '</td>'
							. '</tr>';
				}
			}
		}
		else
			$html .= '<td colspan="4">None</td>';
		$html .= '</tbody></table>';

		return $html;
	}

?>
