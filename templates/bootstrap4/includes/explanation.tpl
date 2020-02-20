{strip}
<div class="col-12" id="explan">
    <div class="row">
        {if isset($dsa_explanation)}
        <div class="col-12 {if isset($dsa_explanation.tabs)} showhint{$dsa_explanation.visable}{/if}">
            {if isset($dsa_explanation.tabs)}
            <ul class="nav nav-tabs">
                {foreach $dsa_explanation.tabs as $id => $tab}
                <li class="nav-item><a href="#tab-{$id}" aria-controls="profile" role="tab" data-toggle="tab" class="nav-link{if $id == 1} active{/if}">{$tab.label}</a></li>
                {/foreach}
            </ul>
            <div class="tab-content">
                {foreach $dsa_explanation.tabs as $id => $tab}
                    <div role="tabpanel" class="tab-pane active" id="tab-{$id}">
                        {if isset($tab.audio.enabled)}
                            <div class="sound fas fa-fw fa-volume-up" id="audioanswer{$tab.audio.file|lower}">
                                <audio id="audio{$tab.audio.file|lower}" preload="auto">
                                    <source src="{$tab.audio.location}/mp3/{$tab.audio.file}.mp3" type="audio/mpeg">
                                    <source src="{$tab.audio.location}/ogg/{$tab.audio.file}.ogg" type="audio/ogg">
                                </audio>
                            </div>
                        {/if}
                        {$tab.text}
                    </div>
                {/foreach}
            </div>
            {elseif isset($dsa_explanation.explanation)}
                <div class="explanation{$dsa_explanation.visable}">
                    {if isset($dsa_explanation.audio.enabled)}
                        <div class="sound fas fa-fw fa-volume-up" id="audioanswer{$dsa_explanation.audio.file|lower}">
                            <audio id="audio{$dsa_explanation.audio.file|lower}" preload="auto">
                                <source src="{$dsa_explanation.audio.location}/mp3/{$dsa_explanation.audio.file}.mp3" type="audio/mpeg">
                                <source src="{$dsa_explanation.audio.location}/ogg/{$dsa_explanation.audio.file}.ogg" type="audio/ogg">
                            </audio>
                        </div>
                    {/if}
                    <strong>Official DVSA answer explanation:</strong>
                    {$dsa_explanation.explanation}
                </div>
            {/if}
        </div>
        {/if}
    </div>
</div>
{/strip}