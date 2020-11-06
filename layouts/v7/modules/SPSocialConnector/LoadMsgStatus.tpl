{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
-->*}
{strip}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right " >
                        <button type="button" class="close" onClick="self.close()" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class='fa fa-close'></span>
                        </button>
                    </div>
                    <div class="pull-left">
                        {"<h4>"|cat:{vtranslate('Import result', $MODULE)}|cat:"</h4>"|cat:"<h3>{$MSG_COUNT}</h3>"|cat:"<br>"|cat:{vtranslate('Messages', $MODULE)}|cat:"<br>"}
                    </div>
                </div>
            </div>

        <div class="modal-footer">
            <div class=" pull-left cancelLinkContainer">
                <button class="btn btn-success" onClick="self.close()"><strong>{vtranslate('LBL_CLOSE', $MODULE)}</strong></button>
            </div>
        </div>
        </div>
    </div>
{/strip}