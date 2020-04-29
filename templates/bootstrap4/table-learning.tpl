{strip}
{nocache}
<table class="table table-striped table-hover table-sm styled bt-0">
    <thead>
    <tr>
        <th>{$table.title}</th>
        <th class="text-center noquestions"><div class="fas fa-question fa-2x fa-fw text-info"></div><span class="d-none d-sm-inline-block ml-1"><br />Questions</span></th>
        <th class="text-center reviewcorrect"><div class="fas fa-check fa-2x fa-fw text-success"></div><span class="d-none d-sm-inline-block ml-1"><br />Correct</span></th>
        <th class="text-center reviewincorrect"><div class="fas fa-times fa-2x fa-fw text-danger"></div><span class="d-none d-sm-inline-block ml-1"><br />Incorrect</span></th>
        <th class="text-center reviewunattempt"><div class="fas fa-exclamation fa-2x fa-fw text-warning"></div><span class="d-none d-sm-inline-block ml-1"><br />Unattempted</span></th>
    </tr>
    </thead>
    {foreach $table.ans as $a => $cat}
    <tr>
        <td><a href="?test={$cat.section}&amp;section={$table.section}" title="{$cat.name}">{$cat.name}</a></td>
        <td class="text-center noquestions">{$cat.numquestions}</td>
        <td class="text-center reviewcorrect">{$cat.correct}</td>
        <td class="text-center reviewincorrect">{$cat.incorrect}</td>
        <td class="text-center reviewunattempt">{$cat.notattempted}</td>
    </tr>
    {/foreach}
    <tr>
        <td></td>
        <td class="text-center noquestions">{$table.totalquestions}</td>
        <td class="text-center reviewcorrect">{$table.totalcorrect}</td>
        <td class="text-center reviewincorrect">{$table.totalincorrect}</td>
        <td class="text-center reviewunattempt">{$table.totalnotattempted}</td>
    </tr>
</table>
{/nocache}
{/strip}