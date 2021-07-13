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

	define('PAGE', S_PROFILE);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	Header("content-type: application/x-javascript");

	$f = popup_features(true, true);
	$addw = get_config('servicetype-add-popup-width', 600);
	$addh = get_config('servicetype-add-popup-height', 400);
	$editw = get_config('servicetype-edit-popup-width', 600);
	$edith = get_config('servicetype-edit-popup-height', 450);
	$vieww = get_config('servicetype-view-popup-width', 500);
	$viewh = get_config('servicetype-view-popup-height', 300);

?>
function ServiceTypeAddPopup(parms, path)
{
	var w = window.open(path + "popups/customers/servicetype/add.php" + parms, "ServiceTypeAdd" + parms,
			"<?php echo $f ?>,width=<?php echo $addw ?>,height=<?php echo $addh ?>");
	w.focus();
}
function ServiceTypeEditPopup(parms, path)
{
	var w = window.open(path + "popups/customers/servicetype/edit.php?" + parms, "ServiceTypeEdit" + parms,
			"<?php echo $f ?>,width=<?php echo $editw ?>,height=<?php echo $edith ?>");
	w.focus();
}
function ServiceTypeViewPopup(parms, path)
{
	var w = window.open(path + "popups/customers/servicetype/view.php?" + parms, "ServiceTypeView" + parms,
			"<?php echo $f ?>,width=<?php echo $vieww ?>,height=<?php echo $viewh ?>");
	w.focus();
}
