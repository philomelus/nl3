<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}css/customertypes.css.php">
        <link rel="stylesheet" href="{$ROOT}customers/css/combined.css">
    </head>
    <body class="w3-container">
        <div class="w3-row w3-display-container" id="header">
            <div class="w3-display-bottomleft">{$path}</div>
            <div class="w3-center w3-xlarge">{$title}</div>
            <div class="w3-display-bottomright">{$username}</div>
        </div>
{include file="menu.tpl"}
        {html_message}
        {html_error}
        {errors}
        <form class="w3-container w3-margin-top w3-center" method="post" action="{$action}">
            <button type="submit" name="action" value="Refresh"><img src="img/refresh.png" alt="Refresh"/></button>
            <button type="submit" name="action" value="Add New" onclick="{php} echo CustomerCombinedAddUrl(); {/php}; return false;"><img src="img/add_s.png" alt="Add New"/></button>
            <table class="w3-table w3-bordered w3-margin-top centered">
                <thead>
                    <tr>
                        <th class="w3-center" colspan="3">{$count}</th>
                        <th>CustID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>CustID</th>
                        <th>Name</th>
                        <th colspan="3">Address</th>
                    </tr>
                </thead>
                <tbody>
{section name=i loop=$combined}
                    <tr>
	{if $combined[i].count === 1}
		{assign var=type value=s}
                        <td class="icon-link">
                            {customer_combined_edit_link id=$combined[i].id path=$ROOT}
                                <img src="{$ROOT}img/edit.png" alt="E" title="Edit this Combined Customer" />
                            </a>
                        </td>
                        <td class="icon-link">
                            <a href="{$action}?action=x&amp;id={$combined[i].id}&amp;id2={$combined[i].others[0].id}">
                                <img src="{$ROOT}img/delete.png" alt="D" title="Delete Combined Customer" />
                            </a>
                        </td>
	{else}
		{assign var=type value=m}
                        <td class="icon-link">&nbsp;</td>
                        <td class="icon-link">
                            <a href="{$action}?action=x&amp;id={$combined[i].id}">
                                <img src="{$ROOT}img/delete.png" alt="D" title="Delete this Combined Customer" />
                            </a>
                        </td>
	{/if}
                        <td class="icon-link">
                            {customer_combined_add_link id=$combined[i].id path=$ROOT}
                                <img src="{$ROOT}img/add.png" alt="V" title="Add another Customer to this Combined Customer" />
                            </a>
                        </td>
                        <td>{$combined[i].id|customer_id}</td>
                        <td>{$combined[i].name}</td>
                        <td>{$combined[i].address}</td>
				
	{if $combined[i].count > 1}
                        <td>{$combined[i].others[0].id|customer_id}</td>
                        <td>{$combined[i].others[0].name}</td>
                        <td>{$combined[i].others[0].address}</td>
                        <td class="icon-link">
                            {customer_combined_edit_link id=$combined[i].id id2=$combined[i].others[0].id path=$ROOT}
                                <img src="{$ROOT}img/edit.png" alt="E" title="Edit this Combined Customer" />
                            </a>
                        </td>
                        <td class="icon-link">
                            <a href="{$action}?action=x&amp;id={$combined[i].id}&amp;id2={$combined[i].others[0].id}">
                                <img src="{$ROOT}img/delete.png" alt="D" title="Delete this Combined Customer" />
                            </a>
                        </td>
		{section name=n loop=$combined[i].others start=1}
			{if $smarty.section.n.last}
				{assign var=type value=ml}
			{/if}
                    </tr>
                    <tr>
                        <td class="icon-link">&nbsp;</td>
                        <td class="icon-link">&nbsp;</td>
                        <td class="icon-link">&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>{$combined[i].others[n].id|customer_id}</td>
                        <td>{$combined[i].others[n].name}</td>
                        <td>{$combined[i].others[n].address}</td>
                        <td class="icon-link">
                            {customer_combined_edit_link id=$combined[i].id id2=$combined[i].others[n].id path=$ROOT}
                                <img src="{$ROOT}img/edit.png" alt="E" title="Edit this Combined Customer" />
                            </a>
                        </td>
                        <td class="icon-link">
                            <a href="{$action}?menu={$menu}&amp;action=x&amp;id={$combined[i].id}&amp;id2={$combined[i].others[n].id}">
                                <img src="{$ROOT}img/delete.png" alt="D" title="Delete this Combined Customer" />
                            </a>
                        </td>
		{/section}
	{else}
                        <td>{$combined[i].others[0].id|customer_id}</td>
                        <td>{$combined[i].others[0].name}</td>
                        <td colspan="3">{$combined[i].others[0].address}</td>
	{/if}
                    </tr>
{/section}
                </tbody>
            </table>
        </form>
        <script src="{$ROOT}js/popups/customers.js.php"></script>
        <script src="{$ROOT}js/popups/customers/combineds.js.php"></script>
    </body>
</html>
