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
            <div class="col-12" id="question-main">
                {if $image.src nocache}<img src="{$image.src}" alt="" width="{$image.width}" height="{$image.height}" class="float-right questionimage img-fluid pl-1" />{/if}
                {include file="includes/mark.tpl" nocache}<br />
                <div class="questiontext" id="{$prim}">
                    {if $question.audio.enabled nocache}
                    <div class="sound fas fa-fw fa-volume-up" id="audioanswer{$question.prim}">
                        <audio id="audio{$question.prim}" preload="auto">
                            <source src="{$question.audio.location}/mp3/{$question.audio.file}.mp3" type="audio/mpeg">
                            <source src="{$question.audio.location}/ogg/{$question.audio.file}.ogg" type="audio/ogg">
                        </audio>
                    </div>
                    {/if}
                    {$question.question nocache}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {foreach $answers as $a => $answer nocache}
                    <div class="option">
                        <div class="answer{if isset($answer.selected) && $answer.selected != false} selected{if $answer.selected != 1}{$answer.selected}{/if}{/if}" id="{$answer.letter}">
                            <div class="selectbtn"></div>
                            {if $answer.audio.enabled}
                            <div class="sound fas fa-fw fa-volume-up" id="audioanswer{$answer.id}">
                                <audio id="audio{$answer.id}" preload="auto">
                                    <source src="{$answer.audio.location}/mp3/{$answer.audio.file}.mp3" type="audio/mpeg">
                                    <source src="{$answer.audio.location}/ogg/{$answer.audio.file}.ogg" type="audio/ogg">
                                </audio>
                            </div>
                            {/if}
                            {$answer.option}
                        </div>
                    </div>
                    {if $a == ceil(($answers|@count)/2)}
                </div>
                <div class="col-md-6">
                    {/if}
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
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}