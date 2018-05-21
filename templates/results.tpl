{strip}
{nocache}
<div id="results">
    <div class="row">
    <h3 class="col-md-12 text-center">{$report.testname} Report</h3>
    <div class="text-center col-md-12">
        {if $results.status == 'pass'}
            <p>Congratulations{if $report.user} {$report.user}{/if}, you have passed this test with {$results.correct}%</p>
            <p>You answered {$results.correct} out of {$results.numquestions} questions correctly</p>
        {else}
            <p>Sorry{if $report.user} {$report.user}{/if}, but you have not passed this time.</p>
            <p>You answered {$results.correct} out of {$results.numquestions} questions correctly, the pass rate is {$report.passmark} out of {$results.numquestions}{if $testType == 'adi'} with at least 20 correct in all 4 of the DVSA categories{/if}</p>
        {/if}
    </div>
    </div>
<div class="panel panel-default">
    <table class="table table-striped">
    {if $report.user}<tr>
        <td><strong>Candidate:</strong></td>
        <td><strong>Date:</strong></td>
    </tr>
    <tr>
        <td>{$report.user}</td>
        <td>{$report.testdate}</td>
    </tr>{/if}
    <tr>
        <td><strong>Test :</strong></td>
        <td><strong>Questions:</strong></td>
    </tr>
    <tr>
        <td>{$report.testname}</td>
        <td><span class="numquestions" id="{$results.numquestions}">{$results.numquestions}</span></td>
    </tr>
    <tr>
        <td><strong>Status:</strong></td>
        <td><strong>Time Taken:</strong></td>
    </tr>
    <tr>
        <td>{$report.status}</td>
        <td>{$report.time}</td>
    </tr>
    </table>
</div>
<div class="panel panel-default">
    <table class="table">
        <tr>
            <td class="text-center col-md-3"><span class="fa fa-check fa-2x fa-fw text-success"></span><strong class="hidden-xs">Correct</strong></td>
            <td class="text-center valign-center">{$results.correct} / {$results.numquestions}</td>
            <td class="valign-center col-md-6"><div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{$results.percent.correct}" aria-valuemin="0" aria-valuemax="100" style="width:{$results.percent.correct}%">{$results.percent.correct}%</div></div></td>
        </tr>
        <tr>
            <td class="text-center"><span class="fa fa-times fa-2x fa-fw text-danger"></span><strong class="hidden-xs">Incorrect</strong></td>
            <td class="text-center valign-center">{math equation="questions - correct" questions=$results.numquestions correct=$results.correct} / {$results.numquestions}</td>
            <td class="valign-center"><div class="progress"><div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="{$results.percent.incorrect}" aria-valuemin="0" aria-valuemax="100" style="width:{$results.percent.incorrect}%">{$results.percent.incorrect}%</div></div></td>
        </tr>
        <tr>
            <td class="text-center"><span class="fa fa-flag fa-2x text-info"></span><strong class="hidden-xs">Flagged</strong></td>
            <td class="text-center valign-center">{$results.flagged} / {$results.numquestions}</td>
            <td class="valign-center"><div class="progress"><div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="{$results.percent.flagged}" aria-valuemin="0" aria-valuemax="100" style="width:{$results.percent.flagged}%">{$results.percent.flagged}%</div></div></td>
        </tr>
        <tr>
            <td class="text-center"><span class="fa fa-exclamation fa-2x text-warning"></span><strong class="hidden-xs">Incomplete</strong></td>
            <td class="text-center valign-center">{$results.incomplete} / {$results.numquestions}</td>
            <td class="valign-center"><div class="progress"><div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{$results.percent.incomplete}" aria-valuemin="0" aria-valuemax="100" style="width:{$results.percent.incomplete}%">{$results.percent.incomplete}%</div></div></td>
        </tr>
    </table>
</div>
<div class="panel panel-default">
<table class="table table-striped">
<thead>
<tr>
<th class="text-left">DVSA Category</th>
<th class="text-center"><span class="fa fa-check fa-fw text-success"></span><span class="hidden-xs">Correct</span></th>
<th class="text-center"><span class="fa fa-times fa-fw text-danger"></span><span class="hidden-xs">Incorrect</span></th>
<th class="text-center"><span class="fa fa-question fa-fw text-info"></span><span class="hidden-xs">Total</span></th>
<th class="text-center">% <span class="hidden-xs">Correct</span></th>
</tr>
</thead>
{foreach $dsa_cat_results as $cat_results}
{$percent = round(($cat_results.correct / $cat_results.total) * 100, 0)}
<tr>
    <td>{$cat_results.section}. {$cat_results.name}</td>
    <td class="text-center">{$cat_results.correct}</td>
    <td class="text-center">{$cat_results.incorrect}</td>
    <td class="text-center">{$cat_results.total}</td>
    <td class="text-center"><img src="data:image/gif;base64,R0lGODlhAQABAJH/AP///wAAAP///wAAACH/C0FET0JFOklSMS4wAt7tACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw==" alt="{$cat_results.name}" width="0" height="20" />{$percent} %</td>
</tr>
{/foreach}
</table>
</div>
</div>
<div class="col-md-12" id="buttons">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="row-eq-height">
                    <div class="col-xs-3">{$review_test}</div>
                    <div class="col-xs-3">{$print_certificate}</div>
                    <div class="col-xs-3"></div>
                    <div class="col-xs-3">{$exit_test}</div>
                    {$extra}
                </div>
            </div>
        </div>
    </div>
</div>
{$script}
{/nocache}
{/strip}