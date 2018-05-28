{strip}
{nocache}
<div class="row">
    <div class="col-lg-12">
        {if $dvsasection}<ul class="nav nav-tabs" role="tablist">
            <li role="tab" class="active"><a href="#hc" data-toggle="tab" title="{$table1name}">{$table1name}</a></li> 
            {if $dvsasection}<li role="tab"><a href="#dvsa" data-toggle="tab" title="{$table2name}">{$table2name}</a></li>{/if}
            {if $l2dsection}<li role="tab"><a href="#l2d" data-toggle="tab" title="{$table3name}">{$table3name}</a></li>{/if}
            {if $reviewsection}<li role="tab"><a href="#cases" data-toggle="tab" title="Case Study">Case Study</a></li>{/if}
        </ul>{/if}
        {if $dvsasection}<div class="tab-content">
            <div class="tab-pane active" id="hc">{/if}{$hcsection}{if $dsasection}</div>
            <div class="tab-pane" id="dvsa">{$dvsasection}</div>
            {if $l2dsection}<div class="tab-pane" id="l2d">{$l2dsection}</div>{/if}
            {if $reviewsection}<div class="tab-pane" id="cases">{$reviewsection}</div>{/if}
        </div>{/if}
    </div>
</div>
{/nocache}
{/strip}