{strip}
{nocache}
<div class="col-12">
    {if isset($alert) || isset($review_questions)}
    <div class="row">
        <div class="col-12">
            {include file="includes/alert.tpl"}
            {include file="includes/review.tpl"}
        </div>
    </div>
    {/if}
    <div id="question-content"{if isset($review_questions)} class="isreview"{/if}>
        <div class="row">
            <div class="col-12">
                {if isset($image.src) nocache}<img src="{$image.src}" alt="" width="273" class="float-right questionimage img-fluid pl-1" />{/if}
                {include file="includes/mark.tpl" nocache}<br />
                <div class="questiontext" id="{$prim}">
                    {if isset($question.audio.enabled) nocache}
                    <div class="sound fas fa-fw fa-volume-up" data-audio-id="{$question.prim}"></div>
                    {/if}
                    <span id="audio{$question.prim}">{$question.question nocache}</span>
                </div>
            </div>
        </div>
        <div id="question-images" class="options">
            <div class="row">
                {foreach $answers as $a => $answer nocache}
                <div class="col-md-6">
                    <div class="option">
                        <div class="answerimage {if isset($answer.selected) && $answer.selected != false} selected img{if $answer.selected != 1}{$answer.selected}{else}selected{/if}{/if}" id="{$answer.letter}">
                            {$answer.option}
                            <img src="{$answer.image.src}" alt="{$answer.option}" width="122" class="img-fluid" />
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="audioswitch audio{if $audio}off{else}on{/if}"><span class="fa-stack fa-lg"><span class="fas fa-volume-up fa-stack-1x"></span>{if $audio}<span class="fas fa-ban fa-stack-2x text-danger"></span>{/if}</span><span class="sr-only">Turn Sound {if $audio}OFF{else}ON{/if}</span></div>
            {include file="includes/mark.tpl" nocache}
        </div>
    </div>
</div>
{include file="includes/buttons.tpl" nocache}
{include file="includes/explanation.tpl" nocache}
<script src="{$script}{if isset($scriptVersion)}?v={$scriptVersion}{/if}"></script>
{/nocache}
{/strip}