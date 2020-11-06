<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

    require_once 'modules/SPCMLConnector/TransactionController.php';
    require_once 'include/utils/VtlibUtils.php';
    
    if(vtlib_isModuleActive("SPCMLConnector")) {
        $userName = $_SERVER['PHP_AUTH_USER'];
        $userPassword = $_SERVER['PHP_AUTH_PW'];
        $transactionController = new TransactionController($userName, $userPassword);
        $transactionStatus = $transactionController->startTransactionStep($_REQUEST);

        echo $transactionStatus;
    }
?>
