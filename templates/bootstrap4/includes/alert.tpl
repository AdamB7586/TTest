{strip}
{if isset($alert)}
    {if $alert == 'flagged' || $alert == 'incomplete'}
        <div class="alert alert-danger">Reviewing {$alert} questions only</div>
    {elseif $alert == 'allmarked'}
        <div class="msg">You have now completed all of the questions, you can mark the test by clicking the "<span class="fas fa-binoculars fa-fw"></span><span class="d-none d-sm-inline-block ml-1"> Review</span>" and then "<span class="endtest"><span class="fas fa-door-open fa-fw"></span><span class="d-none d-sm-inline-block ml-1"> End Test</span></span>" buttons or click on the following button <div class="endtest btn btn-default">Mark my test</div></div>
    {/if}
{/if}
{/strip}