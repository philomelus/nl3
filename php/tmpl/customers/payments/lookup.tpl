<div>
{include file="menu.tpl"}
	<div>
{if (isset($message) && !empty($message))}
        {$message}
{/if}
        {html_error}
        <form name="payments" method="post" action="{$action}">
            <table>
                <tbody>
                    <tr>
                        <td>Amount</td>
                        <td>
                            <select name="amountOp">
                                <option value="lt"{$amountOp.lt}>&lt;</option>
                                <option value="eq"{$amountOp.eq}>=</option>
                                <option value="gt"{$amountOp.gt}>&gt;</option>
                            </select>
                            $<input type="text" name="amount" size="8" value="{$amount}" />
                        </td>
                        <td>&nbsp;</td>
                        <td>Tip</td>
                        <td>
                            <select name="tipOp">
                                <option value="lt"{$tipOp.lt}>&lt;</option>
                                <option value="eq"{$tipOp.eq}>=</option>
                                <option value="gt"{$tipOp.gt}>&gt;</option>
                            </select>
                            $<input type="text" name="tip" size="8" value="{$tip}" />
                        </td>
                    </tr>
                    <tr>
                        <td>ID</td>
                        <td>
                            <input type="text" name="id" maxlength="30" value="{$id}" />
                        </td>
                        <td>&nbsp;</td>
                        <td>Period</td>
                        <td>{html_periods name="iid" selected=$iid, any=true}</td>
                    </tr>
                    <tr>
                        <td>PID</td>
                        <td>
                            <input type="text" name="pid" size="8" maxlength="8" value="{$pid}" />
                        </td>
                        <td>&nbsp;</td>
                        <td>CID</td>
                        <td>
                            <input type="text" name="cid" size="6" maxlength="6" value="{$cid}" />
                        </td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        <td>
                            <select name="type">
                                <option value="{$typeVal[0]}"{$type[0]}>Any</option>
                                <option value="{$typeVal[1]}"{$type[1]}>Check</option>
                                <option value="{$typeVal[2]}"{$type[2]}>Money Order</option>
                                <option value="{$typeVal[3]}"{$type[3]}>Cash</option>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td>Notes</td>
                        <td>
                            <input type="text" name="notes" value="{$notes}" />
                        </td>
                    </tr>
                    <tr>
                        <td>When</td>
                        <td colspan="4">
                            <span>After</span>
                            <span>
                                <input type="text" name="adm" size="2" value="{$adm}" />/<input type="text" name="add" size="2" value="{$add}" />/<input type="text" name="ady" size="4" value="{$ady}" />
                        &nbsp;
                                <input type="text" name="ath" size="2" value="{$ath}" />:<input type="text" name="atm" size="2" value="{$atm}" />:<input type="text" name="ats" size="2" value="{$ats}" />
                            </span>
                            <span>Before</span>
                            <span>
                                <input type="text" name="bdm" size="2" value="{$bdm}" />/<input type="text" name="bdd" size="2" value="{$bdd}" />/<input type="text" name="bdy" size="4" value="{$bdy}" />
                        &nbsp;
                                <input type="text" name="bth" size="2" value="{$bth}" />:<input type="text" name="btm" size="2" value="{$btm}" />:<input type="text" name="bts" size="2" value="{$bts}" /><br />
                            </span>
                        </td>
                    </tr>
                    <tr><td colspan="5"><hr /></td></tr>
                    <tr>
                        <td colspan="3">
                            Show
                            <input type="text" name="limit" size="4" value="{$limit}" />
                            from
                            <input type="text" name="offset" size="4" value="{$offset}" />
                            <input type="submit" name="action" value="&lt;" />
                            <input type="submit" name="action" value="&gt;" />
                        </td>
                        <td colspan="2">
                            <input type="submit" name="action" value="Submit" />
                            <input type="submit" name="action" value="Clear" />
                        </td>
                </tbody>
            </table>
            {$result}
            <input type="hidden" name="menu" value="{$menu}" />
            <input type="hidden" name="submenu" value="{$submenu}" />
        </form>
	</div>
</div>
