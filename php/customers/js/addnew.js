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

// Hookup auto-tab for telephone numbers
$(document).ready(function()
	{
		$('#dt1AreaCode').autotab({ target: 'dt1Prefix', format: 'numeric' });
		$('#dt1Prefix').autotab({ target: 'dt1Number', format: 'numeric', previous: 'dt1AreaCode' });
		$('#dt1Number').autotab({ target: 'dt1Ext', format: 'numeric', previous: 'dt1Prefix' });
		$('#dt1Ext').autotab({ previous: 'dt1Number', format: 'numeric' });

		$('#dt2AreaCode').autotab({ target: 'dt2Prefix', format: 'numeric' });
		$('#dt2Prefix').autotab({ target: 'dt2Number', format: 'numeric', previous: 'dt2AreaCode' });
		$('#dt2Number').autotab({ target: 'dt2Ext', format: 'numeric', previous: 'dt2Prefix' });
		$('#dt2Ext').autotab({ previous: 'dt2Number', format: 'numeric' });

		$('#dt3AreaCode').autotab({ target: 'dt3Prefix', format: 'numeric' });
		$('#dt3Prefix').autotab({ target: 'dt3Number', format: 'numeric', previous: 'dt3AreaCode' });
		$('#dt3Number').autotab({ target: 'dt3Ext', format: 'numeric', previous: 'dt3Prefix' });
		$('#dt3Ext').autotab({ previous: 'dt3Number', format: 'numeric' });
		
		$('#bt1AreaCode').autotab({ target: 'bt1Prefix', format: 'numeric' });
		$('#bt1Prefix').autotab({ target: 'bt1Number', format: 'numeric', previous: 'bt1AreaCode' });
		$('#bt1Number').autotab({ target: 'bt1Ext', format: 'numeric', previous: 'bt1Prefix' });
		$('#bt1Ext').autotab({ previous: 'bt1Number', format: 'numeric' });

		$('#bt2AreaCode').autotab({ target: 'bt2Prefix', format: 'numeric' });
		$('#bt2Prefix').autotab({ target: 'bt2Number', format: 'numeric', previous: 'bt2AreaCode' });
		$('#bt2Number').autotab({ target: 'bt2Ext', format: 'numeric', previous: 'bt2Prefix' });
		$('#bt2Ext').autotab({ previous: 'bt2Number', format: 'numeric' });

		$('#bt3AreaCode').autotab({ target: 'bt3Prefix', format: 'numeric' });
		$('#bt3Prefix').autotab({ target: 'bt3Number', format: 'numeric', previous: 'bt3AreaCode' });
		$('#bt3Number').autotab({ target: 'bt3Ext', format: 'numeric', previous: 'bt3Prefix' });
		$('#bt3Ext').autotab({ previous: 'bt3Number', format: 'numeric' });
	}
);
