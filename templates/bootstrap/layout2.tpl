{strip}
{nocache}
<div class="col-md-12">
    {if $alert || $review_questions}
    <div class="row">
        <div class="col-md-12">
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
    <div class="row">
        <div class="col-md-12">
        {include file="includes/mark.tpl"}
        </div>
    </div>
    <div class="row{if $review_questions} isreview{/if}">
        <div class="col-sm-6"><div id="case"><h4 class="no-margin-t">Case Study</h4>{$case_study}</div></div>
        <div class="col-sm-6">
            <div class="questiontext" id="{$prim}">
                {$question}
            </div>
            {foreach $answers as $a => $answer}
                <div class="answer{if $answer.selected} selected{$answer.selected}{/if}" id="{$answer.letter}">
                    <div class="selectbtn"></div>
                    {if $answer.audio.enabled}
                    <div class="sound fa fa-fw fa-volume-up" id="audioanswer{$answer.id}">
                        <audio id="audio{$answer.id}" preload="auto">
                            <source src="{$answer.audio.location}/mp3/{$answer.audio.file}.mp3" type="audio/mpeg">
                            <source src="{$answer.audio.location}/ogg/{$answer.audio.file}.ogg" type="audio/ogg">
                        </audio>
                    </div>
                    {/if}
                    {$answer.option}
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
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}