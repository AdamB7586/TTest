{strip}
{nocache}
<div class="col-12" id="question-content">
    <div class="col-12">
        {if isset($existing_text)}
            {if $existing_text == 'passed'}
                <p>You have already passed this test! Are you sure you want to start a new test?</p>
            {elseif $existing_text == 'exists'}
                <p>You have already started this test! Would you like to continue this test or start a new one?</p>
            {/if}
        {else}
            <p>Please click the start test button below when you are ready to start. Please make sure you do not navigate away from the page as you will not be able to pick up from where you left the test.</p>
        {/if}
        <div class="timeremaining" data-time="{if isset($seconds)}{$seconds}{/if}"></div>
    </div>
</div>
<div class="col-12" id="buttons">
    <div class="row-eq-height w-100">
        <div class="col-3"><div class="newtest btn btn-theory"><span class="fas fa-sync-alt fa-fw btn-icon"></span><span class="d-none d-sm-inline-block ml-1 btn-text"> Start New Test</span></div></div>
        <div class="col-3"></div>
        <div class="col-3"></div>
        <div class="col-3">{if isset($continue_test)}<div class="continue btn btn-theory" id="{$continue_test}"><span class="fas fa-long-arrow-alt-right fa-fw btn-icon"></span><span class="d-none d-sm-inline-block ml-1 btn-text"> Continue Test</span></div>{/if}</div>
    </div>
</div>
<script src="{$script}{if isset($scriptVersion)}?v={$scriptVersion}{/if}"></script>
{/nocache}
{/strip}