<!DOCTYPE html>
<html>

<body class="body">
<table>
    <tr>
        <td style="padding-right: 25px;">
            <a {href controller="banner" action="redirectbanner" id=$banner.id} target="{$banner->target}" >
                <img src="{$banner->path}" alt="{$banner->name}">
            </a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <a {href controller="banner" action="redirectbanner" id=$banner.id} target="{$banner->target}" >
                {$banner->name}
            </a>
        </td>
    </tr>

</table>
</body>
</html>


