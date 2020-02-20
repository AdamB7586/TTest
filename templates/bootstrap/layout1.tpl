{strip}
{nocache}
<div class="col-md-12">
    {if isset($alert) || isset($review_questions)}
    <div class="row">
        <div class="col-md-12">
            {include file="includes/alert.tpl"}
            {if isset($review_questions)}
                <div class="numreviewq">
                    {foreach $review_questions as $r => $review_question}
                        <div class="questionreview {if $review_question.status == 4}correct{elseif $review_question.status == 3}incorrect{else}incomplete{/if}{if $review_question.current == 4} currentreview{/if}" id="{$review_question.prim}">{$r}</div>
                    {/foreach}
                </div>
            {/if}
        </div>
    </div>
    {/if}
    <div id="question-content"{if isset($review_questions)} class="isreview"{/if}>
        <div class="row">
            <div class="col-sm-12">
                {if $image.src nocache}<img src="{$image.src}" alt="" width="{$image.width}" height="{$image.height}" class="imageright questionimage img-responsive" />{/if}
                {include file="includes/mark.tpl" nocache}<br />
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
            </div>
        </div>
        <div id="question-images">
            <div class="row">
                <div class="options">
                    {foreach $answers as $a => $answer}
                    <div class="col-sm-6">
                        <div class="option">
                            <div class="answerimage {if isset($answer.selected) && $answer.selected != false} selected img{if $answer.selected != 1}{$answer.selected}{else}selected{/if}{/if}" id="{$answer.letter}">
                                {$answer.option}
                                <img src="{$answer.image.src}" alt="{$answer.option}" width="{$answer.image.width}" height="{$answer.image.height}" class="img-responsive" />
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