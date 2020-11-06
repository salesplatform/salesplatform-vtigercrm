{strip}
    <div class="container-fluid">
        <div class=" vt-default-callout vt-info-callout">
            <h4 class="vt-callout-header"><span class="fa fa-info-circle">&nbsp;</span>{vtranslate('LBL_INFORMATION', $QUALIFIED_MODULE)}</h4>
            <p>{$ERROR_MESSAGE}</p>
            <button class="btn btn-info" onclick="location.href='index.php?module=SPTips&view=Index&parent=Settings'"><strong>{vtranslate('LBL_RETURN', $QUALIFIED_MODULE)}</strong></button>
        </div>
    </div>
{/strip}