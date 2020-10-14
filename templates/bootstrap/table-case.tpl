{strip}
<table class="table table-striped table-hover styled">
    {foreach $cases as $i => $case}
    <tr>
        <td><a href="?test={$case.section}&amp;section=casestudy" title="{($i + 1)}. {$case.name}">Case {($i + 1)}. {$case.name}</a></td>
        {assign var="qno" value=1}
        {foreach $case.q as $question}
            <td class="text-center {if $question.status == 0}reviewunattempt{elseif $question.status == 1}reviewincorrect{elseif $question.status == 2}reviewcorrect{/if}" style="width:60px">{$question.num}</td>
{*            {assign var="qno" value=$qno+1}*}
        {/foreach}
        {*{if $qno < 6}
            {for $fill=$qno to 5}
                <td class="text-center">-</td>
            {/for}
        {/if}*}
        </tr>
    {/foreach}
</table>
{/strip}