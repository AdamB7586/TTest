{strip}
{nocache}
<div class="row">
    <div class="col-lg-12">
        {if $dvsasection}<ul class="nav nav-tabs" role="tablist">
            <li role="tab" class="nav-item"><a href="#hc" data-toggle="tab" title="{$table1name}" class="nav-link active">{$table1name}</a></li> 
            {if $dvsasection}<li role="tab" class="nav-item"><a href="#dvsa" data-toggle="tab" title="{$table2name}" class="nav-link">{$table2name}</a></li>{/if}
            {if $l2dsection}<li role="tab" class="nav-item"><a href="#l2d" data-toggle="tab" title="{$table3name}" class="nav-link">{$table3name}</a></li>{/if}
            {if $reviewsection}<li role="tab" class="nav-item"><a href="#cases" data-toggle="tab" title="Case Study" class="nav-link">Case Study</a></li>{/if}
        </ul>{/if}
        {if $dvsasection}<div class="tab-content">
            <div class="tab-pane fade show active" id="hc">{/if}{$hcsection}{if $dvsasection}</div>
            <div class="tab-pane fade" id="dvsa">{$dvsasection}</div>
            {if $l2dsection}<div class="tab-pane fade" id="l2d">{$l2dsection}</div>{/if}
            {if $reviewsection}<div class="tab-pane fade" id="cases">{$reviewsection}</div>{/if}
        </div>{/if}
    </div>
</div>
{/nocache}
{/strip}