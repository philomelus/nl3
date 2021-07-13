<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width:device-width, initial-scale=1">
    <head>
        <title>{$title}</title>
        <link rel="stylesheet" href="{$ROOT}css/w3.css">
        <link rel="stylesheet" href="{$ROOT}css/common.css">
        <link rel="stylesheet" href="{$ROOT}css/errorpage.css">
    </head>
    <body class="w3-container">
        <div class="w3-row w3-display-container" id="header">
            <div class="w3-display-bottomleft">{$path}</div>
            <div class="w3-center w3-xlarge">{$title}</div>
            <div class="w3-display-bottomright">{$username}</div>
        </div>
{include file="menu.tpl"}
        <div class="w3-center">
            <h2>{$msg}</h2>
            <table class="w3-table w3-border w3-bordered">
                <tbody>
                    <caption>{$errText}</caption>
                    <tr>
                        <th>Where</th>
{if empty($errContext)}
                        <td>!!! UNSPECIFIED !!!</td>
{else}
                        <td>{$errContext}</td>
{/if}
                    </tr>
                    <tr>
                        <th>Code</th>
                        <td>{$err}</td>
                    </tr>
                    <tr>
                        <th>Sub-Code</th>
                        <td>{$errCode}</td>
                    </tr>
                    <tr>
                        <th>Query</th>
                        <td>{$errQuery}</td>
                    </tr>
                    <tr>
                        <th>Stack</th>
                        <td>{$errStack}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>

