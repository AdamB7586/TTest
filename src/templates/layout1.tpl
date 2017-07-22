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
    <div id="question-content"{if $review_questions} class="isreview"{/if}>
        <div class="row">
            <div class="col-sm-12">{$image}{$mark}<br />{$question}</div>
        </div>
        <div id="question-images">
            <div class="row">
                <div class="options">
                    <div class="col-sm-6"><div class="option">{$answer_1}</div></div>
                    <div class="col-sm-6"><div class="option">{$answer_2}</div></div>
                </div>
            </div>
            <div class="row">
                <div class="options">
                    <div class="col-sm-6"><div class="option">{$answer_3}</div></div>
                    <div class="col-sm-6"><div class="option">{$answer_4}</div></div>
                </div>
            </div>
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