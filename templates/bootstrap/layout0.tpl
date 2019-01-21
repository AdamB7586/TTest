{strip}
{nocache}
<div class="col-md-12">
    {if $alert || $review_questions}
    <div class="row">
        <div class="col-md-12">
            {$alert}
            {if $review_questions}
                <div class="numreviewq">
                    {foreach $review_questions as $r => $review_question}
                        <div class="questionreview {if $review_question.status == 4}correct{elseif $review_question.status == 3}incorrect{else}incomplete{/if}{if $review_question.current == 4} currentreview{/if}" id="{$review_question.prim}">{$r}</div>
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
    {/if}
    <div id="question-content"{if $review_questions} class="isreview"{/if}>
        <div class="row">
            <div class="col-sm-12" id="question-main">{$image}{$mark}<br />
                <div class="questiontext" id="{$prim}">
                    {$question}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="option">{$answer_1}</div>
                <div class="option">{$answer_2}</div>
                <div class="option">{$answer_3}</div>
            </div>
            <div class="col-sm-6">
                <div class="option">{$answer_4}</div>
                {if $answer_5}<div class="option">{$answer_5}</div>{/if}
                {if $answer_6}<div class="option">{$answer_6}</div>{/if}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="audioswitch audio{if $audio}off{else}on{/if}"><span class="fa-stack fa-lg"><span class="fa fa-volume-up fa-stack-1x"></span>{if $audio}<span class="fa fa-ban fa-stack-2x text-danger"></span>{/if}</span><span class="sr-only">Turn Sound {if $audio}OFF{else}ON{/if}</span></div>
        {$mark}
        </div>
    </div>
</div>
<div class="col-md-12" id="buttons">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="row-eq-height">
                    <div class="col-xs-3">{$previous_question}</div>
                    <div class="col-xs-3">{$flag_question}</div>
                    <div class="col-xs-3">{$review}</div>
                    <div class="col-xs-3">{$next_question}</div>
                    {$extra}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12" id="explan"><div class="row">{$dsa_explanation|strip}</div></div>
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}