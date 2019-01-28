{strip}
{nocache}
<div class="col-12" id="question-content">
    <div class="col-12">
        {if $existing_text == 'passed'}
            <p>You have already passed this test! Are you sure you want to start a new test?</p>
        {elseif $existing_text == 'exists'}
            <p>You have already started this test! Would you like to continue this test or start a new one?</p>
        {else}
            <p>Please click the start test button below when you are ready to start. Please make sure you do not navigate away from the page as you will not be able to pick up from where you left the test.</p>
        {/if}
        <div class="timeremaining" id="{$seconds}"></div>
    </div>
</div>
<div class="col-12" id="buttons">
    <div class="row-eq-height w-100">
        <div class="col-3"><div class="newtest btn btn-theory"><span class="fas fa-refresh fa-fw"></span><span class="hidden-xs"> Start New Test</span></div></div>
        <div class="col-3"></div>
        <div class="col-3"></div>
        <div class="col-3">{if $continue_test}<div class="continue btn btn-theory" id="{$continue_test}"><span class="fas fa-long-arrow-right fa-fw"></span><span class="hidden-xs"> Continue Test</span></div>{/if}</div>
    </div>
</div>
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}