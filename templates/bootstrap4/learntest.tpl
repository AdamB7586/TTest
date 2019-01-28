{strip}
{nocache}
<div class="row">
    <div class="col-lg-10 offset-lg-1 col-12">
        <div class="row">
            <div id="learningTest" class="w-100">
                <div id="testHeader">
                    <span id="questiondata">Q<span class="hidden-xs">uestion </span><span id="qnum">{$question_no}</span><span class="hidden-xs"> of </span><span class="hidden-sm hidden-md hidden-lg">/</span><span id="totalq">{$no_questions}</span></span> <span id="testname">{$test_name}</span>
                </div>
                <div id="question">
                    {$question_data}
                </div>
            </div>
        </div>
    </div>
</div>
{/nocache}
{/strip}