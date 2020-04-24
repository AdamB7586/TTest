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
    <div class="row">
        <div class="col-md-12">
        {include file="includes/mark.tpl"}
        </div>
    </div>
    <div class="row{if isset($review_questions)} isreview{/if}">
        <div class="col-sm-6">
            <div id="case">
                <h4 class="no-margin-t">Case Study</h4>
                {if isset($case_study.audio.enabled) nocache}
                <div class="sound fas fa-fw fa-volume-up" data-audio-id="cs"></div>
                {/if}
                <span id="audiocs">{$case_study.case}</span>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="questiontext" id="{$prim}">
                {if isset($question.audio.enabled) nocache}
                    <div class="sound fas fa-fw fa-volume-up" data-audio-id="{$question.prim}"></div>
                {/if}
                <span id="audio{$question.prim}">{$question.question}</span>
            </div>
            {foreach $answers as $a => $answer}
                <div class="answer{if isset($answer.selected) && $answer.selected != false} selected selected{if $answer.selected != 1}{$answer.selected}{/if}{/if}" id="{$answer.letter}">
                    <div class="selectbtn"></div>
                    {if isset($answer.audio.enabled)}
                    <div class="sound fa fa-fw fa-volume-up" data-audio-id="{$answer.id}"></div>
                    {/if}
                    <span id="audio{$answer.id}">{$answer.option}</span>
                </div>
            {/foreach}
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
<script src="{$script}{if isset($scriptVersion}?v={$scriptVersion}{/if}"></script>
{/nocache}
{/strip}