{strip}
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><span class="page-header-text"><span class="fa fa-graduation-cap"></span> {$test_name}</span></h1>
    </div>
</div>
<div class="row{if !$instructor} hidden-lg{/if}">
    <a href="/student/{if !$instructor}study{else}progress?pupil={$smarty.get.pupil}{/if}" title="Back" class="btn btn-danger" id="backButton"><span class="fa fa-angle-left fa-fw"></span> Back to {if !$instructor}Study &amp; Practice{else}Lesson Progress{/if}</a>
</div>
{if $instructor}<h3>Progress for {$pupilInfo.firstname} {$pupilInfo.surname}</h3>{/if}
{nocache}
<div class="row">
    <div class="col-lg-10 col-lg-offset-1 col-md-12">
        <div class="row">
            <div id="learningTest">
            <div id="testHeader">
                <span id="questiondata">Q<span class="hidden-xs">uestion </span><span id="qnum">{$question_no}</span><span class="hidden-xs"> of </span><span class="hidden-sm hidden-md hidden-lg">/</span><span id="totalq">{$no_questions}</span></span> <span id="testname">{$test_name}</span> 
            </div>
            <div id="question">{$question_data}</div>
            </div>
        </div>
    </div>
</div>
{/nocache}
<div class="row">
    <div class="col-md-12 text-center">
        <small>Crown copyright material reproduced under licence from the Driver and Vehicle Standards Agency, which does not accept any responsibility for the accuracy of the reproduction.</small>
    </div>
</div>
{/strip}