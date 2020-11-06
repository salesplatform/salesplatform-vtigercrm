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

<!-- If include it in module view, in reguest will be parameter ?v=6.0.0 and js and css will not included   -->
<link rel="stylesheet" href="includes/SalesPlatform/CodeMirror/lib/codemirror.css">
<script src="includes/SalesPlatform/CodeMirror/lib/codemirror.js"></script>
<script src="includes/SalesPlatform/CodeMirror/mode/xml/xml.js"></script>
<script src="includes/SalesPlatform/CodeMirror/mode/css/css.js"></script>


<div class="container-fluid">  
    <form name="mainform" action="index.php?module=SPPDFTemplates&action=SavePDFTemplate" method="post" enctype="multipart/form-data">
    <input type="hidden" name="templateid" value="{$MODEL->get('templateid')}">

    <!-- HEADER of Edit view -->
    {if $EMODE eq 'edit'}
        {if $DUPLICATE_FILENAME eq ""}
            <div class="widget_header">
                <h3><b><a href="index.php?module=SPPDFTemplates&view=List">{vtranslate('LBL_TEMPLATE_GENERATOR', $MODULE)}</a> &gt; {vtranslate('LBL_EDIT', $MODULE)} &quot;{$NAME}&quot; </b></h3>
            </div>
                
        {else}
            <div class="widget_header">
                <h3><b><a href="index.php?module=SPPDFTemplates&view=List">{vtranslate('LBL_TEMPLATE_GENERATOR', $MODULE)}</a> &gt; {vtranslate('LBL_DUPLICATE', $MODULE)}&quot;{$DUPLICATE_NAME}&quot; </b></h3>
            </div>
        {/if}
    {else}
            <div class="widget_header">
                <h3><b><a href="index.php?module=SPPDFTemplates&view=List">{vtranslate('LBL_TEMPLATE_GENERATOR', $MODULE)}</a> > {vtranslate('LBL_NEW_TEMPLATE', $MODULE)} </b></h3>
            </div>
    {/if}

    {vtranslate('LBL_TEMPLATE_GENERATOR_DESCRIPTION', $MODULE)}
    <hr>
    <br>
          
    <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0"> 
        <tr>
            <td align="left" valign="top">
                <div style="diplay:block;" id="properties_div">       
                    <table class="table table-bordered">                        
                        <tr>
                            <td width=25% class="small cellLabel"><font color="red">*</font><strong>{vtranslate('LBL_TEMPLATE_NAME', $MODULE)}:</strong></td>
                            <td width=75% class="small cellText"><input name="templatename" id="templatename" type="text" value="{$MODEL->get('name')}" class="detailedViewTextBox" tabindex="1"></td>
                        </tr>
                        <tr>
                            <td valign=top class="small cellLabel"><font color="red">*</font><strong>{vtranslate('LBL_MODULENAMES', $MODULE)}:</strong></td>
                            <td class="cellText small" valign="top">
                                <select name="modulename" id="modulename" class="small">
                                        {if $MODEL->get('module') neq ""}
                                        {html_options  options=$MODULENAMES selected=$MODEL->get('module')}
                                    {else}
                                        {html_options  options=$MODULENAMES}
                                    {/if}
                                </select>
                            </td>      						
                        </tr>    					
                        <tr>
                            <td width=25% class="small cellLabel"><font color="red">*</font><strong>{vtranslate('LBL_HEADER_SIZE', $MODULE)}:</strong></td>
                            <td width=75% class="small cellText"><input name="header_size" id="header_size" type="text" value="{$MODEL->get('header_size')}" class="detailedViewTextBox" tabindex="2" style="width: 100px">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width=25% class="small cellLabel"><font color="red">*</font><strong>{vtranslate('LBL_FOOTER_SIZE', $MODULE)}:</strong></td>
                            <td width=75% class="small cellText"><input name="footer_size" id="footer_size" type="text" value="{$MODEL->get('footer_size')}" class="detailedViewTextBox" tabindex="3" style="width: 100px">&nbsp;</td>
                        </tr>
                        <tr>
                            <td valign=top class="small cellLabel"><font color="red">*</font><strong>{vtranslate('LBL_PAGE_ORIENTATION', $MODULE)}:</strong></td>
                            <td class="cellText small" valign="top">
                                <select name="page_orientation" id="page_orientation" class="small">
                                        {html_options  options=$PAGE_ORIENTATIONS selected=$MODEL->get('page_orientation')}
                                </select>
                            </td>      						
                        </tr>
                        <tr> 
                            <td valign=top class="small cellLabel"><font color="red">*</font><strong>{vtranslate('LBL_COMPANY', 'Settings:Vtiger')}:</strong></td> 
                            <td class="cellText small" valign="top"> 
                                <select name="spcompany" id="spcompany" class="small"> 
                                        {html_options  options=$SP_PDF_COMPANIES selected=$MODEL->get('spcompany')} 
                                </select> 
                            </td> 
                        </tr>                         
                    </table>              
                </div>
            </td>
        </tr>
        
        <tr>
            <td>
                <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0"> 
                   <tr>
                       <td style="text-align:center;padding:15px 0px 10px 0px;">
                          <input type="submit" style="color: white !important;" value="{vtranslate('LBL_SAVE_BUTTON_LABEL', $MODULE)}" class="btn btn-success" onclick="return saveTemplate();"> 
                          <input type="button" style="color: white !important;" value="{vtranslate('LBL_CANCEL_BUTTON_LABEL', $MODULE)}" class="btn btn-danger" onclick="window.history.back();">
                       </td>
                   </tr>
                </table>
            </td>
        </tr>
        
        <tr>
            <td>
                <div style="diplay:block; max-width: 80%" id="body_div2">
                    <style>.CodeMirror {ldelim} border: 1px solid #cccccc; {rdelim}</style>
                    <textarea name="body" id="body" style="width:100%;height:500px" class=small tabindex="5">{$MODEL->get('template')}</textarea>
                </div>
            </td>
        </tr>
        
        <tr>
            <td>
                 <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0"> 
                    <tr>
                        <td style="text-align:center;padding:15px 0px 10px 0px;">
                           <input type="submit" style="color: white !important;" value="{vtranslate('LBL_SAVE_BUTTON_LABEL', $MODULE)}" class="btn btn-success" onclick="return saveTemplate();"> 

                           <input type="button" style="color: white !important;" value="{vtranslate('LBL_CANCEL_BUTTON_LABEL', $MODULE)}" class="btn btn-danger" onclick="window.history.back();">
                        </td>
                    </tr>
                 </table>
            </td>
        </tr>
        
    </table>
    
    </form>
</div>



			
			
<script>
var editor = CodeMirror.fromTextArea(document.getElementById("body"),
{ldelim}
mode: "text/html", tabMode: "indent"
{rdelim}
);

function trim(str)
{ldelim}
        while (str.substring(0,1) == ' ') // check for white spaces from beginning
        {ldelim}
                str = str.substring(1, str.length);
        {rdelim}
        while (str.substring(str.length-1, str.length) == ' ') // check white space from end
        {ldelim}
                str = str.substring(0,str.length-1);
        {rdelim}
        return str;
{rdelim}

function check4null(form)
{ldelim}

        var isError = false;
        var errorMessage = "";
        // Here we decide whether to submit the form.
        if (trim(form.templatename.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{vtranslate('LBL_TEMPLATE_NAME', $MODULE)}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.modulename.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{vtranslate('LBL_MODULENAMES', $MODULE)}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.header_size.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{vtranslate('LBL_HEADER_SIZE', $MODULE)}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.footer_size.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{vtranslate('LBL_FOOTER_SIZE', $MODULE)}";
                form.templatename.focus();
        {rdelim}

        if (trim(form.page_orientation.value) =='') {ldelim}
                isError = true;
                errorMessage += "\n " + "{vtranslate('LBL_PAGE_ORIENTATION', $MODULE)}";
                form.templatename.focus();
        {rdelim}
            
        // Here we decide whether to submit the form.
        if (isError == true) {ldelim}
                alert("{vtranslate('LBL_MISSING_FIELDS', $MODULE)}" + errorMessage);
                return false;
        {rdelim}
 return true;

{rdelim}

function saveTemplate()
{ldelim}
    
    if (!check4null(document.mainform))
       return false;
    else
       return true;
{rdelim}


</script>
