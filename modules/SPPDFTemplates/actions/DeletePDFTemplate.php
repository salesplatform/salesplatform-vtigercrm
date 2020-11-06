<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  SalesPlatform vtiger CRM Open Source
 * The Initial Developer of the Original Code is SalesPlatform.
 * Portions created by SalesPlatform are Copyright (C) SalesPlatform.
 * All Rights Reserved.
 ************************************************************************************/

class SPPDFTemplates_DeletePDFTemplate_Action extends Vtiger_Delete_Action {
    
    public function process(\Vtiger_Request $request) {
        $idlist = $request->get('idlist');
        $idArray=explode(';', $idlist);
        foreach ($idArray as $id) {
            SPPDFTemplates_Record_Model::deleteById($id);
        }
        
        /* Display List view */
        header("Location:index.php?module=SPPDFTemplates&view=List");
    }
    
}

?>
