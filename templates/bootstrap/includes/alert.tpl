{if isset($alert)}
    {if $alert == 'flagged' || $alert == 'incomplete'}
        <div class="alert alert-danger">Reviewing {$alert} questions only</div>
    {elseif $alert == 'allmarked'}
        <div class="msg">You have now completed all of the questions, you can mark the test by clicking the "<span class="fa fa-binoculars fa-fw"></span><span class="hidden-xs"> Review</span>" and then "<span class="endtest"><span class="fa fa-sign-out fa-fw"></span><span class="hidden-xs"> End Test</span></span>" buttons or click on the following button <div class="endtest btn btn-default">Mark my test</div></div>
    {/if}
{/if}