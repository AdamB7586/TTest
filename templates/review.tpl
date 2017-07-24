{strip}
{nocache}
<div class="col-md-12" id="question-content">
<div class="col-md-12">
<table class="table">
<tr>
<td><span class="fa fa-question fa-fw fa-2x text-info"></span></td>
<td class="valign-center"><strong>Total number of test questions:</strong></td>
<td class="valign-center text-center"><strong>{$test_questions}</strong></td>
</tr>
<tr>
<td><span class="fa fa-tasks fa-fw fa-2x text-success"></span></td>
<td class="valign-center"><strong>Number of completed questions:</strong></td>
<td class="valign-center text-center"><strong>{$complete_questions}</strong></td>
</tr>
<tr>
<td><span class="fa fa-exclamation fa-fw fa-2x text-warning"></span></td>
<td class="valign-center"><strong>Number of incomplete questions:</strong></td>
<td class="valign-center text-center"><strong>{$incomplete_questions}</strong></td>
</tr>
<tr>
<td><span class="fa fa-flag fa-fw fa-2x text-danger"></span></td>
<td class="valign-center"><strong>Number of flagged questions:</strong></td>
<td class="valign-center text-center"><strong>{$flagged_questions}</strong></td>
</tr>
</table>
</div>
</div>
<div class="col-md-12" id="buttons">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="row-eq-height">
                    <div class="col-xs-3">{$review_all}</div>
                    <div class="col-xs-3">{$review_incomplete}</div>
                    <div class="col-xs-3">{$review_flagged}</div>
                    <div class="col-xs-3">{$end_test}</div>
                </div>
            </div>
        </div>
    </div>
</div>
{$script}
{/nocache}
{/strip}