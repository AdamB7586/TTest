{strip}
<div class="col-12" id="buttons">
    <div class="row-eq-height w-100">
        <div class="col-3">
            {if isset($previous_question) && is_array($previous_question)}
            <div class="btn btn-theory prevquestion{if isset($previous_question.class)} {$previous_question.class}{/if}" id="{$previous_question.id}">
                <span class="fas fa-{$previous_question.icon} fa-fw btn-icon"></span>
                <span class="d-none d-sm-inline-block ml-1 btn-text"> {$previous_question.text}</span>
            </div>
            {else}
            <div class="noprev"></div>
            {/if}
        </div>
        <div class="col-3">
            {if isset($flag_question) && is_array($flag_question)}
                <div class="btn btn-theory{if isset($flag_question.class)}  {$flag_question.class}{/if}">
                    <span class="fas fa-{$flag_question.icon} fa-fw btn-icon"></span>
                    <span class="d-none d-sm-inline-block ml-1 btn-text"> {$flag_question.text}</span>
                </div>
            {/if}
        </div>
        <div class="col-3">
            {if isset($review) && is_array($review)}
                <div class="btn btn-theory{if isset($review.class)} {$review.class}{/if}">
                    <span class="fas fa-{$review.icon} fa-fw btn-icon"></span>
                    <span class="d-none d-sm-inline-block ml-1 btn-text"> {$review.text}</span>
                </div>
            {/if}
        </div>
        <div class="col-3">
            {if isset($next_question) && is_array($next_question)}
            <div class="btn btn-theory nextquestion{if isset($next_question.class)} {$next_question.class}{/if}" id="{$next_question.id}">
                <span class="fas fa-{$next_question.icon} fa-fw btn-icon"></span>
                <span class="d-none d-sm-inline-block ml-1 btn-text"> {$next_question.text}</span>
            </div>
            {/if}
        </div>
        {include file="includes/extra.tpl"}
    </div>
</div>
{/strip}