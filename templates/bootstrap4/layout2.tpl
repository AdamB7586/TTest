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
    <div class="row">
        <div class="col-12">
        {include file="includes/mark.tpl" nocache}
        </div>
    </div>
    <div class="row{if isset($review_questions)} isreview{/if}">
        <div class="col-md-6">
            <div id="case">
                <h4 class="no-margin-t">Case Study</h4>
                {if isset($case_study.audio.enabled) nocache}
                <div class="sound fas fa-fw fa-volume-up" id="audioanswercs">
                    <audio id="audiocs" preload="auto">
                        <source src="{$case_study.audio.location}/mp3/{$case_study.audio.file}.mp3" type="audio/mpeg">
                        <source src="{$case_study.audio.location}/ogg/{$case_study.audio.file}.ogg" type="audio/ogg">
                    </audio>
                </div>
                {/if}
                {$case_study.case}
            </div>
        </div>
        <div class="col-md-6">
            <div class="questiontext" id="{$prim}">
                {if isset($question.audio.enabled) nocache}
                <div class="sound fas fa-fw fa-volume-up" id="audioanswer{$question.prim}">
                    <audio id="audio{$question.prim}" preload="auto">
                        <source src="{$question.audio.location}/mp3/{$question.audio.file}.mp3" type="audio/mpeg">
                        <source src="{$question.audio.location}/ogg/{$question.audio.file}.ogg" type="audio/ogg">
                    </audio>
                </div>
                {/if}
                {$question.question}
            </div>
            {foreach $answers as $a => $answer nocache}
                <div class="answer{if isset($answer.selected) && $answer.selected != false} selected selected{if $answer.selected != 1}{$answer.selected}{/if}{/if}" id="{$answer.letter}">
                    <div class="selectbtn"></div>
                    {if isset($answer.audio.enabled)}
                    <div class="sound fas fa-fw fa-volume-up" id="audioanswer{$answer.id}">
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
        <div class="col-12">
            <div class="audioswitch audio{if $audio}off{else}on{/if}"><span class="fa-stack fa-lg"><span class="fas fa-volume-up fa-stack-1x"></span>{if $audio}<span class="fas fa-ban fa-stack-2x text-danger"></span>{/if}</span><span class="sr-only">Turn Sound {if $audio}OFF{else}ON{/if}</span></div>
            {include file="includes/mark.tpl" nocache}
        </div>
    </div>
</div>
{include file="includes/buttons.tpl" nocache}
{include file="includes/explanation.tpl" nocache}
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}