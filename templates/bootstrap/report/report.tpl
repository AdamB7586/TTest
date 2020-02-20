{strip}
<div class="row">
    <div class="col-lg-12">
        <table class="table table-striped table-bordered table-condensed">
            <thead>
            <tr>
                <th>Theory Test</th>
                <th class="text-center">Mark</th>
                <th colspan="2" class="text-center">Status</th>
                <th class="text-center">Review</th>
            </tr>
            </thead>
            {foreach $testReports as $i => $test}
                {if isset($test.status)}{if $test.status == 1}{$result = "Passed"}{else}{$result = "Failed"}{/if}{/if}
            <tr>
                <td><a href="?test={$i}">{if isset($test.status) && $test.status >= 1}Retake {/if}Theory Test {$i}</a></td>
                <td class="text-center"><strong>{if isset($test.totalscore)}{$test.totalscore|string_format:"%d"}{else}0{/if} / {if !isset($adiReport)}50{else}100{/if}</strong></td>
                {if isset($test.status) && $test.status >= 1}<td class="text-center {$result|strtolower}">{$result}</td><td class="text-center"><a href="/certificate.pdf?testID={$i}" title="View PDF" target="_blank"><span class="fa fa-file-pdf-o"></span> View PDF {if $test.status == 1}Certificate{else}Report{/if}</a></td>
                {else}<td class="text-center" colspan="2">Unattempted</td>{/if}
                <td class="text-center">{if isset($test.status) && $test.status >= 1}<a href="?report=true&amp;id={$i}" title="Review Test">Review Test</a>{/if}</td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
{/strip}