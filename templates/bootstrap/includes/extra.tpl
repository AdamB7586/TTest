{strip}
    {if isset($extra)}
        {if isset($extra.skipCorrect)}
    </div>
</div>
<div class="row">
    <div class="col-xs-12 skipcorrectclear">
        <div class="btn btn-theory skipcorrect{if isset($extra.flagged)}{$extra.flagged}{/if}">Skip Correct</div>
        {/if}
        <div class="signal signal{if isset($extra.signal)}{$extra.signal}{/if}"></div>
    {/if}
{/strip}