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

	define('PAGE', SA_PERIODS);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	Header("content-type: application/x-javascript");

	$f = popup_features(true, true);
	$addw = get_config('period-add-popup-width', 400);
	$addh = get_config('period-add-popup-height', 300);
	$editw = get_config('period-edit-popup-width', 400);
	$edith = get_config('period-edit-popup-height', 300);
	$vieww = get_config('period-view-popup-width', 400);
	$viewh = get_config('period-view-popup-height', 300);

?>
function PeriodAddPopup(path)
{
	var w = window.open(path + "popups/periods/add.php", "PeriodAdd",
			"<?php echo $f ?>,width=<?php echo $addw ?>,height=<?php echo $addh ?>");
	w.focus();
}
function PeriodEditPopup(parms, path)
{
	var w = window.open(path + "popups/periods/edit.php?" + parms, "PeriodEdit" + parms,
			"<?php echo $f ?>,width=<?php echo $editw ?>,height=<?php echo $edith ?>");
	w.focus();
}
function PeriodViewPopup(parms, path)
{
	var w = window.open(path + "popups/periods/view.php?" + parms, "PeriodView" + parms,
			"<?php echo $f ?>,width=<?php echo $vieww ?>,height=<?php echo $viewh ?>");
	w.focus();
}
