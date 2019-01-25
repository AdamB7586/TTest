{strip}
{nocache}
<div class="col-12">
    {if $alert || $review_questions}
    <div class="row">
        <div class="col-12">
            {include file="includes/alert.tpl"}
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
            <div class="col-12">
                {if $image}<img src="{$imagesrc}" alt="" width="{$imagewidth}" height="{$imageheight}" class="imageright questionimage img-fluid" />{/if}
                {include file="includes/mark.tpl"}<br />
                <div class="questiontext" id="{$prim}">
                    {$question}
                </div>
            </div>
        </div>
        <div id="question-images">
            <div class="row">
                <div class="options">
                    {foreach $answers as $a => $answer}
                    <div class="col-sm-6">
                        <div class="option">
                            <div class="answerimage {if $answer.selected} img{$answer.selected}{/if}" id="{$answer.letter}">
                                {$answer.option}
                                <img src="{$answer.image.src}" alt="{$answer.option}" width="{$answer.image.width}" height="{$answer.image.height}" class="img-fluid" />
                            </div>
                        </div>
                    </div>
                    {if $a is div by 2 && $a !== $answers|@count}
                </div>
            </div>
            <div class="row">
                <div class="options">
                    {/if}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="audioswitch audio{if $audio}off{else}on{/if}"><span class="fa-stack fa-lg"><span class="fa fa-volume-up fa-stack-1x"></span>{if $audio}<span class="fa fa-ban fa-stack-2x text-danger"></span>{/if}</span><span class="sr-only">Turn Sound {if $audio}OFF{else}ON{/if}</span></div>
            {include file="includes/mark.tpl"}
        </div>
    </div>
</div>
{include file="includes/buttons.tpl"}
{include file="includes/explanation.tpl"}
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}