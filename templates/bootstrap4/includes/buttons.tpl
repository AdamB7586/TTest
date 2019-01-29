{strip}
<div class="col-12" id="buttons">
    <div class="row-eq-height w-100">
        <div class="col-xs-3">
            {if $previous_question}
            <div class="btn btn-theory prevquestion {$previous_question.class}" id="{$previous_question.id}">
                <span class="fas fa-{$previous_question.icon} fa-fw"></span>
                <span class="hidden-xs"> {$previous_question.text}</span>
            </div>
            {else}
            <div class="noprev"></div>
            {/if}
        </div>
        <div class="col-xs-3">
            <div class="btn btn-theory {$flag_question.class}">
                <span class="fas fa-{$flag_question.icon} fa-fw"></span>
                <span class="hidden-xs"> {$flag_question.text}</span>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="btn btn-theory {$review.class}">
                <span class="fas fa-{$review.icon} fa-fw"></span>
                <span class="hidden-xs"> {$review.text}</span>
            </div>
        </div>
        <div class="col-xs-3">
            {if $previous_question}
            <div class="btn btn-theory nextquestion {$next_question.class}" id="{$next_question.id}">
                <span class="fas fa-{$next_question.icon} fa-fw"></span>
                <span class="hidden-xs"> {$next_question.text}</span>
            </div>
            {/if}
        </div>
        {include file="includes/extra.tpl"}
    </div>
</div>
{/strip}