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

	//-------------------------------------------------------------------------
	// Handle customer map page display
	function display()
	{
		global $customer;

?>
		<div>
			<div></div>
		</div>
<?php
	}

	//-------------------------------------------------------------------------
	// Return customer map page specific scripts
	function scripts()
	{
		global $customer, $deliveryAddr;

		return '';
/*
'
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA_VgPbRlrBVBdx7wVVTgCbBREpZ8fMSpYO7DIWaZhZ6MS2Xw1PhSIknQ_0rJujfg1oDOxJXTOAiOIuQ" type="text/javascript"></script>
<script type="text/javascript">
    //<![CDATA[
    var map = null;
    var geocoder = null;

	function load()
	{
		if (GBrowserIsCompatible())
		{
			map = new GMap2(document.getElementById("map"));
			map.addControl(new GSmallMapControl());
			map.setCenter(new GLatLng(37.4419, -122.1419), 13);
			geocoder = new GClientGeocoder();
			if (geocoder)
			{
				geocoder.getLatLng(
					"' . $deliveryAddr->address1 . ', ' . $deliveryAddr->zip . '",
					function(point)
					{
						if (!point)
						{
							alert(address + " not found");
						}
						else
						{
							map.setCenter(point, 13);
							var marker = new GMarker(point);
							map.addOverlay(marker);
							marker.openInfoWindowHtml(address);
						}
					}
				);
			}
		}
	}
    //]]>
</script>
';
*/
	}

	//-------------------------------------------------------------------------
	// Return customer map page specific styles
	function styles()
	{
		return '';
	}

	//-------------------------------------------------------------------------
	// Handle customer map page submits
	function submit()
	{
	}

?>
