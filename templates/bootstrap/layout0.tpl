{strip}
{nocache}
<div class="col-md-12">
    {if isset($alert) || isset($review_questions)}
    <div class="row">
        <div class="col-md-12">
            {include file="includes/alert.tpl"}
            {include file="includes/review.tpl"}
        </div>
    </div>
    {/if}
    <div id="question-content"{if isset($review_questions)} class="isreview"{/if}>
        <div class="row">
            <div class="col-sm-12" id="question-main">
                {if isset($image.src) nocache}<img src="{$image.src}" alt="" width="273" class="imageright questionimage img-responsive" />{/if}
                {include file="includes/mark.tpl" nocache}<br />
                <div class="questiontext" id="{$prim}">
                    {if isset($question.audio.enabled) nocache}
                    <div class="sound fas fa-fw fa-volume-up" data-audio-id="{$question.prim}"></div>
                    {/if}
                    <span id="audio{$question.prim}">{$question.question nocache}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                {foreach $answers as $a => $answer}
                    <div class="option">
                        <div class="answer{if isset($answer.selected) && $answer.selected != false} selected selected{if $answer.selected != 1}{$answer.selected}{/if}{/if}" id="{$answer.letter}">
                            <div class="selectbtn"></div>
                            {if isset($answer.audio.enabled)}
                            <div class="sound fa fa-fw fa-volume-up" data-audio-id="{$answer.id}"></div>
                            {/if}
                            <span id="audio{$answer.id}">{$answer.option}</span>
                        </div>
                    </div>
                    {if $a == ceil(($answers|@count)/2)}
                </div>
                <div class="col-sm-6">
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="audioswitch audio{if $audio}off{else}on{/if}"><span class="fa-stack fa-lg"><span class="fa fa-volume-up fa-stack-1x"></span>{if $audio}<span class="fa fa-ban fa-stack-2x text-danger"></span>{/if}</span><span class="sr-only">Turn Sound {if $audio}OFF{else}ON{/if}</span></div>
        {include file="includes/mark.tpl"}
        </div>
    </div>
</div>
{include file="includes/buttons.tpl"}
{include file="includes/explanation.tpl"}
<script src="{$script}{if isset($scriptVersion)}?v={$scriptVersion}{/if}"></script>
{/nocache}
{/strip}