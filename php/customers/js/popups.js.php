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
	
	define('PAGE', SC_SEARCH);
	
	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	
	Header("content-type: application/x-javascript");
	
	$pf = popup_features(true, true);
	$adjw = get_config('customer-adjustment-add-popup-width', 700);
	$adjh = get_config('customer-adjustment-add-popup-height', 500);
	$typew = get_config('customer-type-add-popup-width', 550);
	$typeh = get_config('customer-type-add-popup-height', 500);
	$bitchw = get_config('customer-complaint-add-popup-width', 700);
	$bitchh = get_config('customer-complaint-add-popup-height', 500);
	$ssw = get_config('customer-service-add-popup-width', 550);
	$ssh = get_config('customer-service-add-popup-height', 500);
	$fsw = get_config('flagstop-service-add-popup-width', 550);
	$fsh = get_config('flagstop-service-add-popup-height', 500);
	
?>
function EditCustomer()
{
	var c = document.getElementById("cid");
	if (c)
		CustomerEditPopup('cid=' + c.value, '')
}
function PopupAdjustment(url,target)
{
	var w = window.open(url, target, "<?php echo $pf ?>,width=<?php echo $adjw ?>,height=<?php echo $adjh ?>");
	w.focus();
}
function PopupChangeType(url,target)
{
	var w = window.open(url, target, "<?php echo $pf ?>,width=<?php echo $typew ?>,height=<?php echo $typeh ?>");
	w.focus();
}
function PopupComplaint(url,target)
{
	var w = window.open(url, target, "<?php echo $pf ?>,width=<?php echo $bitchw ?>,height=<?php echo $bitchh ?>");
	w.focus();
}
function PopupStartStop(url,target)
{
	var w = window.open(url, target, "<?php echo $pf ?>,width=<?php echo $fsw ?>,height=<?php echo $fsh ?>");
	w.focus();
}
function PopupStopStart(url,target)
{
	var w = window.open(url, target, "<?php echo $pf ?>,width=<?php echo $ssw ?>,height=<?php echo $ssh ?>");
	w.focus();
}
function ViewCustomer()
{
	var c = document.getElementById("cid");
	if (c)
		CustomerViewPopup('cid=' + c.value, '')
}
