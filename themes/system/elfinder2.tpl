<!DOCTYPE public>
<html>
<head>
    <title>ElFinder 2</title>
</head>
<body>

<script type="text/javascript" src="/misc/require-jquery.js"></script>
<script type="text/javascript">
    require.config({
        baseUrl: '/static',
        paths : {
            "misc" : "../misc",
            "jui" : "misc/"
        },
        deps: [
            "jquery",
            "jui"
        ],

    });
</script>
</body>
</html>