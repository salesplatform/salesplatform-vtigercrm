{*
/*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: SalesPlatform Ltd
* The Initial Developer of the Original Code is SalesPlatform Ltd.
* All Rights Reserved.
* If you have any questions or comments, please email: devel@salesplatform.ru
************************************************************************************/
*}
{strip}
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> 
        <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="module" value="{$MODULE}">
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" name="action" value="SaveProvider" />
            <input type="hidden" name="record" value="{$PROVIDER->getId()}" />

            <div class="editViewHeader">
                <div class='row'>
                    <div class="col-lg-12 col-md-12 col-sm-12 ">
                        <h4 class="editHeader">{vtranslate('LBL_EDIT_PROVIDER', $QUALIFIED_MODULE)}</h4>
                    </div>
                </div>
            </div>
            <hr>
            <div class="editViewBody">
                <div class="editViewContents" >
                    <table class="table table-borderless">
                        <tr>
                            <td>
                                <label class="control-label fieldLabel col-sm-4">{vtranslate('LBL_PROVIDER_NAME', $QUALIFIED_MODULE)}</label>
                                <input class="fieldValue inputElement" type="text" disabled="disabled" name="provider_name" value="{$PROVIDER->getName()}"/>
                            </td>
                        </tr>
                        {foreach key=FIELD_NAME item=VALUE from=$PROVIDER->getSettingsMap()}
                            <tr>
                                <td>
                                    <label class="control-label fieldLabel col-sm-4">{vtranslate($FIELD_NAME, $QUALIFIED_MODULE)}</label>
                                    <input class="fieldValue inputElement" type="text" name="{$FIELD_NAME}" value="{$VALUE}"/>
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>
            <div class='modal-overlay-footer clearfix'>
                <div class="row clearfix">
                    <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                        <button type='submit' class='btn btn-success saveButton'  >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                        <a class='cancelLink'  href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/strip}