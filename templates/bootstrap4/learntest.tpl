{strip}
{nocache}
<div class="row">
    <div class="col-lg-10 offset-lg-1 col-12">
        <div id="learningTest" class="w-100">
            <div id="testHeader">
                <span id="questiondata">Q<span class="d-none d-sm-inline-block">uestion&nbsp;</span><span id="qnum">{$question_no}</span><span class="d-none d-sm-inline-block">&nbsp;of&nbsp;</span><span class="d-inline-block d-sm-none">/</span><span id="totalq">{$no_questions}</span></span> <span id="testname">{$test_name}</span>
            </div>
            <div id="question">
                {$question_data}
            </div>
        </div>
    </div>
</div>
{/nocache}
{/strip}