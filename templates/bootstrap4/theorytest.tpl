{strip}
{nocache}
<div class="row">
    <div class="col-lg-10 offset-lg-1 col-12">
        <div id="theoryTest" class="w-100">
            <div class="row" id="testHeader">
                <div class="col" id="testname">{$test_name}</div>
                {if $report != 'true'}<div class="col text-center" id="questiondata">Q<span class="d-none d-sm-inline-block">uestion</span> <span id="qnum">{$question_no}</span> of <span id="totalq">{$no_questions}</span></div>{/if}
                {if $report != 'true'}<div class="col text-right" id="countdown">Time <span class="d-none d-sm-inline-block">remaining</span>: <span id="time">57:00</span></div>{/if}
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