<script type="text/javascript">
(function(){
    {if $timeout}
    setTimeout( function(){
        window.location.href = "$url";
    }, $timeout );
    {else}
    window.location.href = "$url";
    {/if}
})();
</script>
