/*
	Copyright 2020 Russell E. Gibson

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

function week_changed()
{
    var weekCtrl = document.getElementById("week");
    if (weekCtrl)
    {
        // Update displayed dates
        var index = parseInt(weekCtrl.value);
        for (var i = 0; i < 7; ++i)
        {
            var thCtrl = document.getElementById("dd" + String(i));
            if (thCtrl)
                thCtrl.firstChild.textContent = returnWeeks[index][i + 1]; 
            var thCtrl = document.getElementById("rd" + String(i));
            if (thCtrl)
                thCtrl.firstChild.textContent = returnWeeks[index][i + 1]; 
        }

        // Update hidden input with timestamp of first date
        var input = document.getElementById("sow");
        if (input)
            input.value = returnWeeks[index][0];
    }
}

// Called when value of draw count changed in one of the
// draws tables number inputs.  Updates the total count
// for the day of the week.
// TODO:  This is hard coded in every sense definable
//        If this ever needs changing, it should be converted
//        to dynamic row count ... etc.
function draw_changed(col, row)
{
    var tCol = String(col);
    var r0 = document.getElementById("d" + tCol + "0");
    var r1 = document.getElementById("d" + tCol + "1");
    var r2 = document.getElementById("d" + tCol + "2");
    var r3 = document.getElementById("d" + tCol + "3");
    var t0 = document.getElementById("dt" + tCol);

    var total = 0;
    if (r0 && r1 && r2 && r3)
    {
        total = parseInt(r0.value)
                + parseInt(r1.value)
                + parseInt(r2.value)
                + parseInt(r3.value);
    }
    if (t0)
        t0.firstChild.textContent = String(total);
}

// Called when value of return count changed in one of the
// returns tables number inputs.  Updates the total count
// for the day of the week.
// TODO:  This is hard coded in every sense definable
//        If this ever needs changing, it should be converted
//        to dynamic row count ... etc.
function return_changed(col, row)
{
    var tCol = String(col);
    var r0 = document.getElementById("r" + tCol + "0");
    var r1 = document.getElementById("r" + tCol + "1");
    var r2 = document.getElementById("r" + tCol + "2");
    var r3 = document.getElementById("r" + tCol + "3");
    var t0 = document.getElementById("rt" + tCol);

    var total = 0;
    if (r0 && r1 && r2 && r3)
    {
        total = parseInt(r0.value)
                + parseInt(r1.value)
                + parseInt(r2.value)
                + parseInt(r3.value);
    }
    if (t0)
        t0.firstChild.textContent = String(total);
}

