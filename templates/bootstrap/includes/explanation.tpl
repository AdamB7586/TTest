{strip}
<div class="col-md-12" id="explan">
    <div class="row">
        {if isset($dsa_explanation)}
        <div class="col-md-12 {if isset($dsa_explanation.tabs)} showhint{$dsa_explanation.visable}{/if}">
            {if isset($dsa_explanation.tabs)}
            <ul class="nav nav-tabs">
                {foreach $dsa_explanation.tabs as $id => $tab}
                <li{if $id == 1} class="active"{/if}><a href="#tab-{$id}" aria-controls="profile" role="tab" data-toggle="tab">{$tab.label}</a></li>
                {/foreach}
            </ul>
            <div class="tab-content">
                {foreach $dsa_explanation.tabs as $id => $tab}
                    <div role="tabpanel" class="tab-pane active" id="tab-{$id}">
                        {if isset($tab.audio.enabled)}
                            <div class="sound fa fa-fw fa-volume-up" data-audio-id="{$tab.audio.file|lower}"></div>
                        {/if}
                        <span id="audio{$tab.audio.file|lower}">{$tab.text}</span>
                    </div>
                {/foreach}
            </div>
            {elseif isset($dsa_explanation.explanation)}
                <div class="explanation{$dsa_explanation.visable}">
                    {if isset($dsa_explanation.audio.enabled)}
                        <div class="sound fa fa-fw fa-volume-up" data-audio-id="{$dsa_explanation.audio.file|lower}"></div>
                    {/if}
                    <span id="audio{$dsa_explanation.audio.file|lower}">
                        <strong>Official DVSA answer explanation:</strong>
                        {$dsa_explanation.explanation}
                    </span>
                </div>
            {/if}
        </div>
        {/if}
    </div>
</div>
{/strip}