<div>
{include file="menu.tpl"}
    <div>
{html_error}
{if !empty($message) || count($errorList) > 0}
        <div>
	{if !empty($message)}{$message}{/if}
	{if count($errorList) > 0}
		{section name=err loop=$errorList}
            <div>{$errorList[err]}</div>
		{/section}
	{/if}
        </div>
{/if}
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
    <h1>Returns</h1>
    <div>
        <label for="week">Week</label>
        <select name="week" onchange="week_changed();">
{foreach from=$options item=option}
            {$option}
{/foreach}
        </select>
    </div>
    <table>
        <thead>
            <tr>
               <th>Route</th>
               <th>Sun</th>
               <th>Mon</th>
               <th>Tue</th>
               <th>Wed</th>
               <th>Thu</th>
               <th>Fri</th>
               <th>Sat</th>
            </tr>
            <tr>
                <th>DRAW</th>
                <th id="dd0">{$days[0]}</th>
                <th id="dd1">{$days[1]}</th>
                <th id="dd2">{$days[2]}</th>
                <th id="dd3">{$days[3]}</th>
                <th id="dd4">{$days[4]}</th>
                <th id="dd5">{$days[5]}</th>
                <th id="dd6">{$days[6]}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>GEA103SC</th>
                <td>
                    <input type="number" value="0" name="d00" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(0, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d10" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(1, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d20" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(2, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d30" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(3, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d40" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(4, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d50" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(5, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d60" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(6, 0); return false;" />
                </td>
            </tr>
            <tr>
                <th>GEA121SC</th>
                <td>
                    <input type="number" value="0" name="d01" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(0, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d11" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(1, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d21" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(2, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d31" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(3, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d41" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(4, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d51" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(5, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d61" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(6, 1); return false;" />
                </td>
            </tr>
            <tr>
                <th>GEA138SC</th>
                <td>
                    <input type="number" value="0" name="d02" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(0, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d12" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(1, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d22" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(2, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d32" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(3, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d42" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(4, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d52" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(5, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d62" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(6, 2); return false;" />
                </td>
            </tr>
            <tr>
                <th>GEA146SC</th>
                <td>
                    <input type="number" value="0" name="d03" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(0, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d13" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(1, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d23" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(2, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d33" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(3, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d43" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(4, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d53" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(5, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="d63" size="3" maxlength="3" min="0" max="999" onchange="draw_changed(6, 3); return false;" />
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <td id="dt0">0</td>
                <td id="dt1">0</td>
                <td id="dt2">0</td>
                <td id="dt3">0</td>
                <td id="dt4">0</td>
                <td id="dt5">0</td>
                <td id="dt6">0</td>
            </tr>
        </tfoot>
    </table>
    <table>
        <thead>
            <tr>
               <th>Route</th>
               <th>Sun</th>
               <th>Mon</th>
               <th>Tue</th>
               <th>Wed</th>
               <th>Thu</th>
               <th>Fri</th>
               <th>Sat</th>
            </tr>
            <tr>
                <th>RETURNS</th>
                <th id="rd0">{$days[0]}</th>
                <th id="rd1">{$days[1]}</th>
                <th id="rd2">{$days[2]}</th>
                <th id="rd3">{$days[3]}</th>
                <th id="rd4">{$days[4]}</th>
                <th id="rd5">{$days[5]}</th>
                <th id="rd6">{$days[6]}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>GEA103SC</th>
                <td>
                    <input type="number" value="0" name="r00" size="3" maxlength="3" min="0" max="999" onchange="return_changed(0, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r10" size="3" maxlength="3" min="0" max="999" onchange="return_changed(1, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r20" size="3" maxlength="3" min="0" max="999" onchange="return_changed(2, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r30" size="3" maxlength="3" min="0" max="999" onchange="return_changed(3, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r40" size="3" maxlength="3" min="0" max="999" onchange="return_changed(4, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r50" size="3" maxlength="3" min="0" max="999" onchange="return_changed(5, 0); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r60" size="3" maxlength="3" min="0" max="999" onchange="return_changed(6, 0); return false;" />
                </td>
            </tr>
            <tr>
                <th>GEA121SC</th>
                <td>
                    <input type="number" value="0" name="r01" size="3" maxlength="3" min="0" max="999" onchange="return_changed(0, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r11" size="3" maxlength="3" min="0" max="999" onchange="return_changed(1, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r21" size="3" maxlength="3" min="0" max="999" onchange="return_changed(2, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r31" size="3" maxlength="3" min="0" max="999" onchange="return_changed(3, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r41" size="3" maxlength="3" min="0" max="999" onchange="return_changed(4, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r51" size="3" maxlength="3" min="0" max="999" onchange="return_changed(5, 1); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r61" size="3" maxlength="3" min="0" max="999" onchange="return_changed(6, 1); return false;" />
                </td>
            </tr>
            <tr>
                <th>GEA138SC</th>
                <td>
                    <input type="number" value="0" name="r02" size="3" maxlength="3" min="0" max="999" onchange="return_changed(0, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r12" size="3" maxlength="3" min="0" max="999" onchange="return_changed(1, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r22" size="3" maxlength="3" min="0" max="999" onchange="return_changed(2, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r32" size="3" maxlength="3" min="0" max="999" onchange="return_changed(3, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r42" size="3" maxlength="3" min="0" max="999" onchange="return_changed(4, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r52" size="3" maxlength="3" min="0" max="999" onchange="return_changed(5, 2); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r62" size="3" maxlength="3" min="0" max="999" onchange="return_changed(6, 2); return false;" />
                </td>
            </tr>
            <tr>
                <th>GEA146SC</th>
                <td>
                    <input type="number" value="0" name="r03" size="3" maxlength="3" min="0" max="999" onchange="return_changed(0, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r13" size="3" maxlength="3" min="0" max="999" onchange="return_changed(1, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r23" size="3" maxlength="3" min="0" max="999" onchange="return_changed(2, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r33" size="3" maxlength="3" min="0" max="999" onchange="return_changed(3, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r43" size="3" maxlength="3" min="0" max="999" onchange="return_changed(4, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r53" size="3" maxlength="3" min="0" max="999" onchange="return_changed(5, 3); return false;" />
                </td>
                <td>
                    <input type="number" value="0" name="r63" size="3" maxlength="3" min="0" max="999" onchange="return_changed(6, 3); return false;" />
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <td id="rt0">0</td>
                <td id="rt1">0</td>
                <td id="rt2">0</td>
                <td id="rt3">0</td>
                <td id="rt4">0</td>
                <td id="rt5">0</td>
                <td id="rt6">0</td>
            </tr>
        </tfoot>
    </table>
    <input type="submit" name="action" value="Send" />
    <input type="hidden" name="sow" value="{$selectedTimestamp}" />
</form>

