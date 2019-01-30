{strip}
<table class="table table-striped table-hover table-sm styled bt-0">
    {foreach $cases as $case}
    <tr>
        <td><a href="?test={$case.section}&amp;section=casestudy" title="{$case.section}. {$case.name}">Case {$case.section}. {$case.name}</a></td>
        {foreach $case.q as $question}
            <td class="text-center {if $question.status == 0}reviewunattempt{elseif $question.status == 1}reviewincorrect{elseif $question.status == 2}reviewcorrect{/if}" style="width:60px">{$question.num}</td>
        {/foreach}
        </tr>
    {/foreach}
</table>
{/strip}