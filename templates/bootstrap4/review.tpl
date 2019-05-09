{strip}
{nocache}
<div class="col-12" id="question-content">
<div class="col-12">
<table class="table">
<tr>
<td class="border-top-0"><span class="fas fa-question fa-fw fa-2x text-info"></span></td>
<td class="border-top-0" class="valign-center"><strong>Total number of test questions:</strong></td>
<td class="border-top-0 valign-center text-center"><strong>{$test_questions}</strong></td>
</tr>
<tr>
<td><span class="fas fa-tasks fa-fw fa-2x text-success"></span></td>
<td class="valign-center"><strong>Number of completed questions:</strong></td>
<td class="valign-center text-center"><strong>{$complete_questions}</strong></td>
</tr>
<tr>
<td><span class="fas fa-exclamation fa-fw fa-2x text-warning"></span></td>
<td class="valign-center"><strong>Number of incomplete questions:</strong></td>
<td class="valign-center text-center"><strong>{$incomplete_questions}</strong></td>
</tr>
<tr>
<td><span class="fas fa-flag fa-fw fa-2x text-danger"></span></td>
<td class="valign-center"><strong>Number of flagged questions:</strong></td>
<td class="valign-center text-center"><strong>{$flagged_questions}</strong></td>
</tr>
</table>
</div>
</div>
<div class="col-12" id="buttons">
    <div class="row-eq-height w-100">
        <div class="col-3"><div class="reviewall btn btn-theory" id="{$review_all}"><span class="fas fa-sync-alt fa-fw btn-icon"></span><span class="d-none d-sm-inline-block ml-1 btn-text"> Review All</span></div></div>
        <div class="col-3"><div class="reviewincomplete btn btn-theory" id="{$review_incomplete}"><span class="fas fa-tasks fa-fw btn-icon"></span><span class="d-none d-sm-inline-block ml-1 btn-text"> Review Incomplete</span></div></div>
        <div class="col-3"><div class="reviewflagged btn btn-theory" id="{$review_flagged}"><span class="fas fa-flag fa-fw btn-icon"></span><span class="d-none d-sm-inline-block ml-1 btn-text"> Review Flagged</span></div></div>
        <div class="col-3"><div class="endtest btn btn-theory"><span class="fas fa-door-open fa-fw btn-icon"></span><span class="d-none d-sm-inline-block ml-1 btn-text"> End Test</span></div></div>
    </div>
</div>
<script type="text/javascript" src="{$script}"></script>
{/nocache}
{/strip}