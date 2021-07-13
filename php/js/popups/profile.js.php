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

	define('PAGE', S_PROFILE);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';

	Header("content-type: application/x-javascript");

	$f = popup_features(true, true);
	$papw = get_config('profile-add-popup-width', 500);
	$paph = get_config('profile-add-popup-height', 350);
	$pepw = get_config('profile-edit-popup-width', 500);
	$peph = get_config('profile-edit-popup-height', 350);

?>
function ProfileAddPopup(path)
{
	var w = window.open(path + "popups/profile/add.php", "ProfileAdd",
			"<?php echo $f ?>,width=<?php echo $papw ?>,height=<?php echo $paph ?>");
	w.focus();
}
function ProfileEditPopup(parms, path)
{
	var w = window.open(path + "popups/profile/edit.php?" + parms, "ProfileEdit" + parms,
			"<?php echo $f ?>,width=<?php echo $pepw ?>,height=<?php echo $peph ?>");
	w.focus();
}
