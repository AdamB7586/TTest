{strip}
{nocache}
<div class="col-md-12">
    {if $alert || $review_questions}
    <div class="row">
        <div class="col-md-12">
            {$alert}
            {$review_questions}
        </div>
    </div>
    {/if}
    <div class="row">
        <div class="col-md-12">
        {$mark}
        </div>
    </div>
    <div class="row{if $review_questions} isreview{/if}">
        <div class="col-sm-6"><div id="case"><h4 class="no-margin-t">Case Study</h4>{$case_study}</div></div>
        <div class="col-sm-6">
            {$question}
            {$answer_1}
            {$answer_2}
            {$answer_3}
            {$answer_4}
            {$answer_5}
            {$answer_6}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
        {$audio}{$mark}
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
<div class="col-md-12" id="explan"><div class="row">{$dsa_explanation}</div></div>
{$script}
{/nocache}
{/strip}