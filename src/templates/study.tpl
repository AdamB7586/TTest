{strip}
{nocache}
<div class="row">
<div class="col-lg-12">
    <h1 class="page-header"><span class="page-header-text"><span class="fa fa-graduation-cap"></span> Study &amp; Practice</span></h1>
</div>
<div class="col-lg-12 hidden-md hidden-lg">
    <a href="/student/" title="Back" class="btn btn-danger" id="backButton"><span class="fa fa-angle-left fa-fw"></span> Back to Dashboard</a>
</div>
<div class="col-lg-12">
    {if $dsasection}<ul class="nav nav-tabs" role="tablist">
        <li role="tab" class="active"><a href="#hc" data-toggle="tab" title="{$table1name}">{$table1name}</a></li> 
        {if $dsasection}<li role="tab"><a href="#dvsa" data-toggle="tab" title="{$table2name}">{$table2name}</a></li>{/if}
        {if $ldcsection}<li role="tab"><a href="#l2d" data-toggle="tab" title="{$table3name}">{$table3name}</a></li>{/if}
        {if $reviewsection}<li role="tab"><a href="#cases" data-toggle="tab" title="Case Study">Case Study</a></li>{/if}
    </ul>{/if}
    {if $dsasection}<div class="tab-content">
        <div class="tab-pane active" id="hc">{/if}{$hcsection}{if $dsasection}</div>
        <div class="tab-pane" id="dvsa">{$dsasection}</div>
        {if $ldcsection}<div class="tab-pane" id="l2d">{$ldcsection}</div>{/if}
        {if $reviewsection}<div class="tab-pane" id="cases">{$reviewsection}</div>{/if}
    </div>{/if}
</div>
    <div class="col-md-12 text-center">
        <small>Crown copyright material reproduced under licence from the Driver and Vehicle Standards Agency, which does not accept any responsibility for the accuracy of the reproduction.</small>
    </div>
</div>
{/nocache}
{/strip}