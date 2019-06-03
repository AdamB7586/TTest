{strip}
<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped table-bordered table-hover table-sm">
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
                <td><a href="?test={$i}">{if $test.status >= 1}Retake {/if}Theory Test {$i}</a></td>
                <td class="text-center"><strong>{$test.totalscore|string_format:"%d"} / {if !isset($adiReport)}50{else}100{/if}</strong></td>
                {if $test.status >= 1}<td class="text-center bg-{if $result|strtolower == 'failed'}danger{else}success{/if}">{$result}</td><td class="text-center"><a href="/certificate.pdf?testID={$i}" title="View PDF" target="_blank"><span class="fas fa-file-pdf-o"></span> View PDF {if $test.status == 1}Certificate{else}Report{/if}</a></td>
                {else}<td class="text-center" colspan="2">&nbsp;</td>{/if}
                <td class="text-center">{if $test.status >= 1}<a href="?report=true&amp;id={$i}" title="Review Test">Review Test</a>{/if}</td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
{/strip}