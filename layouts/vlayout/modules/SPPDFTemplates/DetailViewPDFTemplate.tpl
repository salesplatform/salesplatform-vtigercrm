{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/
-->*}

<div class="container-fluid">  
        <div class="widget_header">
                <h3><b><a href="index.php?module=SPPDFTemplates&view=List">{vtranslate('LBL_TEMPLATE_GENERATOR', $MODULE)}</a> &gt; {vtranslate('LBL_VIEWING', $MODULE)} &quot;{$MODEL->get('name')}&quot; </b></h3>
        </div>
        <hr>
        <br>
        
        <table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
            <tr>
                    <td><strong>{vtranslate('LBL_PROPERTIES', $MODULE)} &quot;{$MODEL->get('name')}&quot; </strong></td>
                    <td class="small" align=right>&nbsp;&nbsp;
                      <button class="btn btn-success" onclick="location.href='index.php?module=SPPDFTemplates&view=Edit&templateid={$MODEL->getId()}'">{vtranslate('LBL_EDIT_BUTTON_LABEL', $MODULE)}</button>
                      <button class="btn addButton" onclick="location.href='index.php?module=SPPDFTemplates&view=Edit&isDuplicate=true&templateid={$MODEL->getId()}'">{vtranslate('LBL_DUPLICATE_BUTTON', $MODULE)}</button>
                    </td>
            </tr>
        </table>
        <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0"> 
        <tr>
            <td align="left" valign="top">
                <div style="diplay:block;" id="properties_div">       
                    <table class="table table-bordered">                        
                        <tr>
                            <td width=25% class="small cellLabel"><strong>{vtranslate('LBL_TEMPLATE_NAME', $MODULE)}:</strong></td>
                            <td width=75% class="small cellText">{$MODEL->get('name')}</td>
                        </tr>
                        <tr>
                            <td valign=top class="small cellLabel"><strong>{vtranslate('LBL_MODULENAMES', $MODULE)}:</strong></td>
                            <td class="cellText small" valign="top">{vtranslate($MODEL->get('module'))}</td>      						
                        </tr>    					
                        <tr>
                            <td width=25% class="small cellLabel"><strong>{vtranslate('LBL_HEADER_SIZE', $MODULE)}:</strong></td>
                            <td width=75% class="small cellText">{$MODEL->get('header_size')}</td>
                        </tr>
                        <tr>
                            <td width=25% class="small cellLabel"><strong>{vtranslate('LBL_FOOTER_SIZE', $MODULE)}:</strong></td>
                            <td width=75% class="small cellText">{$MODEL->get('footer_size')}</td>
                        </tr>
                        <tr>
                            <td valign=top class="small cellLabel"><strong>{vtranslate('LBL_PAGE_ORIENTATION', $MODULE)}:</strong></td>
                           
                                {if $MODEL->get('page_orientation') eq "P"}
                                     <td class="cellText small" valign="top">{vtranslate("Portrait")}</td>
                                {else}
                                     <td class="cellText small" valign="top">{vtranslate("Landscape")}</td>
                                {/if}      						
                        </tr>    
                        <tr> 
                            <td valign=top class="small cellLabel"><strong>{vtranslate('LBL_COMPANY', 'Settings:Vtiger')}:</strong></td> 
                            <td class="cellText small" valign=top>{vtranslate($MODEL->get('spcompany'), 'Settings:Vtiger')}</td> 
                        </tr> 
                    </table>              
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2" valign=top class="cellText small">
                <br>
                 <table class="thickBorder table-bordered" width="100%"  border="0" cellspacing="0" cellpadding="5" >
                    <tr>
                      <td colspan="2" valign="top" class="small" style="background-color:#cccccc"><strong>{vtranslate('LBL_PDF_TEMPLATE', $MODULE)}</strong></td>
                    </tr>

                    <tr>
                      <td valign="top" class="cellLabel small">{vtranslate('LBL_BODY', $MODULE)}</td>
                      <td class="cellText  small">{decode_html($MODEL->get('template'))}</td>
                    </tr>

                 </table>
            </td>
        </tr>

    </table>		
</div>
