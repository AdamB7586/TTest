{strip}
{nocache}
<div class="row">
    <div class="col-xl-10 offset-xl-1 col-12">
        <div id="theoryTest" class="w-100">
            <div class="row" id="testHeader">
                <div class="col d-none d-sm-inline-block" id="testname">{$test_name}</div>
                {if $report != 'true'}<div class="col text-md-center" id="questiondata">Q<span class="d-none d-lg-inline-block ">uestion&nbsp;</span><span id="qnum">{$question_no}</span> of <span id="totalq">{$no_questions}</span></div>{/if}
                {if $report != 'true'}<div class="col text-right" id="countdown">Time<span class="d-none d-xl-inline-block">&nbsp;remaining</span>: <span id="time">57:00</span></div>{/if}
            </div>
            <div id="question">
                {$question_data}
            </div>
        </div>
    </div>
</div>
{/nocache}
{if $report != 'true'}<script src="{$js_script_location}testtimer.js{if isset($scriptVersion)}?v={$scriptVersion}{/if}"></script>{/if}
{/strip}