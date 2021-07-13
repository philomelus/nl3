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
	set_include_path('../../..' . PATH_SEPARATOR . get_include_path());

	require_once 'inc/security.inc.php';

	define('PAGE', SC_VIEW);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	Header("content-type: application/x-javascript");

	$f = popup_features(true, true);
	$addw = get_config('adjustment-add-popup-width', 500);
	$addh = get_config('adjustment-add-popup-height', 325);
	$editw = get_config('adjustment-edit-popup-width', 500);
	$edith = get_config('adjustment-edit-popup-height', 400);
	$vieww = get_config('adjustment-view-popup-width', 500);
	$viewh = get_config('adjustment-view-popup-height', 250);

?>
function AdjustmentAddPopup(parms, path)
{
	var w = window.open(path + "popups/customers/adjustments/add.php" + parms, "AdjustmentAdd" + parms,
			"<?php echo $f ?>,width=<?php echo $addw ?>,height=<?php echo $addh ?>");
	w.focus();
}
function AdjustmentEditPopup(parms, path)
{
	var w = window.open(path + "popups/customers/adjustments/edit.php?" + parms, "AdjustmentEdit" + parms,
			"<?php echo $f ?>,width=<?php echo $editw ?>,height=<?php echo $edith ?>");
	w.focus();
}
function AdjustmentViewPopup(parms, path)
{
	var w = window.open(path + "popups/customers/adjustments/view.php?" + parms, "AdjustmentView" + parms,
			"<?php echo $f ?>,width=<?php echo $vieww ?>,height=<?php echo $viewh ?>");
	w.focus();
}
