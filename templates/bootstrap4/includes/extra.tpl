{strip}
    {if isset($extra)}
        {if isset($extra.skipCorrect)}
    </div>
</div>
<div class="row">
    <div class="col-12 skipcorrectclear">
        <div class="btn btn-theory skipcorrect{$extra.flagged}">Skip Correct</div>
        {/if}
        <div class="signal signal{$extra.signal}"></div>
    {/if}
{/strip}