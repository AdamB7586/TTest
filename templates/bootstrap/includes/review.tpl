{strip}
{if isset($review_questions) && is_array($review_questions)}
    <div class="numreviewq">
        {foreach $review_questions as $r => $review_question}
            <div class="questionreview {if $review_question.status == 4}correct{elseif $review_question.status == 3}incorrect{else}incomplete{/if}{if $review_question.current == 4} currentreview{/if}" id="{$review_question.prim}">{$r}</div>
        {/foreach}
    </div>
{/if}
{/strip}