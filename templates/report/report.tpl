{strip}
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><span class="page-header-text"><span class="fa fa-pencil"></span> Theory Tests</span></h1>
    </div>
    <div class="col-lg-12">
        {if $newToUpload}<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>You have tests not yet uploaded to your online account storage to share with the LDC <a href="https://itunes.apple.com/gb/artist/learner-driving-centres/id566280394" title="iOS Apps" target="_blank">iOS</a>, <a href="https://play.google.com/store/apps/developer?id=Teaching+Driving+Limited" title="Android Apps" target="_blank">Android Apps</a> and LDC PC software <a href="syncappdata?upload=true" title="Sync app data" class="btn btn-success">Upload new tests</a></div>
        {elseif $newTestsAvailable}<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>You have newer tests taken available on the server to download do you want to <a href="syncappdata" title="Sync app data" class="btn btn-success">Download new tests</a> now?</div>{/if}
        <div class="panel panel-default">
            <div class="panel-heading">Theory Tests</div>
        <table class="table table-striped table-bordered table-condensed styled">
            <thead>
            <tr>
                <th>Theory Test</th>
                <th class="text-center">Mark</th>
                <th colspan="2" class="text-center">Status</th>
                <th class="text-center">Review</th>
            </tr>
            </thead>
            {foreach $testReports as $i => $test}
                {if $test.status == 1}{$result = "Passed"}{else}{$result = "Failed"}{/if}
            <tr>
                <td><a href="theory?test={$i}">{if $test.status >= 1}Retake {/if}Theory Test {$i}</a></td>
                <td class="text-center"><strong>{$test.totalscore|string_format:"%d"} / {if !$adiReport}50{else}100{/if}</strong></td>
                {if $test.status >= 1}<td class="text-center {$result|strtolower}">{$result}</td><td class="text-center"><a href="certificate.pdf?testID={$i}" title="View PDF" target="_blank"><span class="fa fa-file-pdf-o"></span> View PDF {if $test.status == 1}Certificate{else}Report{/if}</a></td>
                {else}<td class="text-center" colspan="2">&nbsp;</td>{/if}
                <td class="text-center">{if $test.status >= 1}<a href="theory?report=true&amp;id={$i}" title="Review Test">Review Test</a>{/if}</td>
            </tr>
            {/foreach}
        </table>
        </div>
    </div>
    <div class="col-md-12 text-center">
        <small>Crown copyright material reproduced under licence from the Driver and Vehicle Standards Agency, which does not accept any responsibility for the accuracy of the reproduction.</small>
    </div>
</div>
{/strip}