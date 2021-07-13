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

// Hookup auto-tab for telephone number
/*
$(document).ready(function()
	{
		$('#areacode').autotab({ target: 'prefix', format: 'numeric' });
		$('#prefix').autotab({ target: 'extension', format: 'numeric', previous: 'areacode' });
		$('#extension').autotab({ target: 'local', format: 'numeric', previous: 'prefix' });
		$('#local').autotab({ previous: 'extension', format: 'numeric' });
	}
);
*/


var dateStop;
var dateStopM;
var dateStopD;
var dateStopY;

var dateStart;
var dateStartM;
var dateStartD;
var dateStartY;

var addStart;
var addStop;

var submit;

$(document).ready(function()
    {
        dateStop = document.getElementById("dateStop");
        dateStopM = document.getElementById("dateStopm");
        dateStopD = document.getElementById("dateStopd");
        dateStopY = document.getElementById("dateStopy");

        dateStart = document.getElementById("dateStart");
        dateStartM = document.getElementById("dateStartm");
        dateStartD = document.getElementById("dateStartd");
        dateStartY = document.getElementById("dateStarty");

        addStop = document.getElementById("addStop");
        addStart = document.getElementById("addStart");

        submit = document.getElementById("action");

        dateStopM.style.backgroundColor = '#cccccc';
        dateStopD.style.backgroundColor = '#cccccc';
        dateStopY.style.backgroundColor = '#cccccc';

        dateStartM.style.backgroundColor = '#cccccc';
        dateStartD.style.backgroundColor = '#cccccc';
        dateStartY.style.backgroundColor = '#cccccc';

        addStopChanged();
        addStartChanged();

    });


function addStopChanged()
{
    var checked = addStop.checked;
    var color = checked ? '#ffffdf' : '#eeeeee';
    var disabled = checked ? false : true;

    dateStop.disabled = disabled;
    dateStopM.disabled = disabled;
    dateStopM.style.backgroundColor = color;
    dateStopD.disabled = disabled;
    dateStopD.style.backgroundColor = color;
    dateStopY.disabled = disabled;
    dateStopY.style.backgroundColor = color;

    enableSubmit();
}

function addStartChanged()
{
    var checked = addStart.checked;
    var color = checked ? '#ffffdf' : '#eeeeee';
    var disabled = checked ? false : true;

    dateStart.disabled = disabled;
    dateStartM.disabled = disabled;
    dateStartM.style.backgroundColor = color;
    dateStartD.disabled = disabled;
    dateStartD.style.backgroundColor = color;
    dateStartY.disabled = disabled;
    dateStartY.style.backgroundColor = color;

    enableSubmit();
}

function enableSubmit()
{
    submit.disabled = (!addStop.checked && !addStart.checked);
}

