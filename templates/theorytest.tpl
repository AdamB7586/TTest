{strip}
{nocache}
<div class="row">
    <div class="col-lg-10 col-lg-offset-1 col-md-12">
        <div class="row">
            <div id="theoryTest">
                <div id="testHeader">
                    <span id="testname">{$test_name}</span> <span id="questiondata">{if $report != 'true'}Q<span class="hidden-xs">uestion </span><span id="qnum">{$question_no}</span> of <span id="totalq">{$no_questions}</span>{/if}</span> {if $report != 'true'}<span id="countdown">Time<span class="hidden-xs"> remaining</span>: <span id="time">57:00</span></span>{/if}
                </div>
                <div id="question">{$question_data}</div>
            </div>
        </div>
    </div>
</div>
{/nocache}
{if $report != 'true'}<script type="text/javascript" src="{$js_script_location}testtimer.js"></script>{/if}
<div class="row">
    <div class="col-md-12 text-center">
        <small>Crown copyright material reproduced under licence from the Driver and Vehicle Standards Agency, which does not accept any responsibility for the accuracy of the reproduction.</small>
    </div>
</div>
{/strip}