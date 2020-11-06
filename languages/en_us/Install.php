<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

$languageStrings = array(
	'ERR_DATABASE_CONNECTION_FAILED' => 'Unable to connect to database Server',
	'ERR_DB_NOT_FOUND' => 'This Database is not found.Try changing the Database settings',
	'ERR_DB_NOT_UTF8'  => 'Database charset or collation not compatible with UTF8',
	'ERR_INVALID_MYSQL_PARAMETERS' => 'Invalid mySQL Connection Parameters specified',
	'ERR_INVALID_MYSQL_VERSION' => 'MySQL version is not supported, kindly connect to MySQL 5.1.x or above',
	'ERR_UNABLE_CREATE_DATABASE' => 'Unable to Create database',
        // SalesPlatform.ru begin #5732
	'ERR_DB_SQLMODE_NOTFRIENDLY' => 'MySQL Server should not be configured with:<br> sql_mode = ONLY_FULL_GROUP_BY, STRICT_TRANS_TABLES, NO_ZERO_IN_DATE, NO_ZERO_DATE',
	// SalesPlatform.ru end
        'LBL_ADMIN_INFORMATION'=>'Admin User Information',
	'LBL_ADMIN_USER_INFORMATION' => 'Admin User Information',
	'LBL_CHOOSE_LANGUAGE' => 'Choose the default language for this installation:',
	'LBL_CONFIRM_CONFIGURATION_SETTINGS' => 'Confirm Configuration Settings',
	'LBL_CREATE_NEW_DB'=>'Create new database',
	'LBL_CURRENCIES'=>'Currency',
	'LBL_CURRENCY' => 'Currency',
	'LBL_DATABASE_INFORMATION' => 'Database Information',
	'LBL_DATABASE_TYPE' => 'Database Type',
	'LBL_DATE_FORMAT'=>'Date format',
	'LBL_DB_NAME' => 'Database Name',
	'LBL_DISAGREE' => 'Disagree',
	'LBL_EMAIL' => 'Email',
	'LBL_GD_LIBRARY' => 'GD Library Support',
	'LBL_HOST_NAME' => 'Host Name',
	'LBL_I_AGREE' => 'I Agree',
	'LBL_IMAP_SUPPORT' => 'Imap Support',
	'LBL_INSTALLATION_IN_PROGRESS'=>'Installation in progress',
	'LBL_INSTALLATION_WIZARD' => 'Installation Wizard',
	'LBL_INSTALL_BUTTON' => 'Install',
	'LBL_INSTALL_PREREQUISITES' => 'Installation prerequisites',
	'LBL_MORE_INFORMATION' => 'More Information',
	'LBL_NEXT' => 'Next',
	'LBL_ONE_LAST_THING' => 'One last thing...',
	'LBL_PASSWORD_MISMATCH' => 'Please re-enter passwords.  The \"Password\" and \"Re-type password\" values do not match.',
	'LBL_PASSWORD' => 'Password',
	'LBL_PHP_CONFIGURATION' => 'PHP Configuration',
	'LBL_PHP_RECOMMENDED_SETTINGS'=>'Recommended PHP Settings',
	'LBL_PHP_VERSION' => 'PHP Version',
	'LBL_PLEASE_WAIT'=>'Please wait',
	'LBL_PRESENT_VALUE' => 'Present Value',
	'LBL_READ_WRITE_ACCESS' => 'Read/Write Access',
	'LBL_RECHECK' => 'Recheck',
	'LBL_REQUIRED_VALUE' => 'Required Value',
	'LBL_RETYPE_PASSWORD' => 'Retype Password',
	'LBL_ROOT_PASSWORD' => 'Root Password',
	'LBL_ROOT_USERNAME' => 'Root User Name',
	'LBL_SYSTEM_CONFIGURATION' => 'System Configuration',
	'LBL_SYSTEM_INFORMATION' => 'System Information',
	'LBL_TIME_ZONE' => 'Time Zone',
	'LBL_TRUE' => 'True',
	'LBL_URL' => 'URL',
	'LBL_USERNAME' => 'User Name',
	'LBL_VTIGER7_SETUP_WIZARD_DESCRIPTION' => 'This wizard will guide you through the installation of Vtiger CRM7',
	'LBL_WELCOME_TO_VTIGER7_SETUP_WIZARD' => 'Welcome to Vtiger CRM 7 Setup Wizard',
	'LBL_WELCOME' => 'Welcome',
	'LBL_ZLIB_SUPPORT' => 'Zlib Support',
	'LBL_SIMPLEXML' => 'SimpleXML Support',
	'MSG_DB_PARAMETERS_INVALID' => 'specified database user, password, hostname, database type, or port is invalid',
	'MSG_DB_ROOT_USER_NOT_AUTHORIZED' => 'Message: Specified database Root User doesn\'t have permission to Create database or the Database name has special characters. Try changing the Database settings',
	'MSG_DB_USER_NOT_AUTHORIZED' => 'specified database user does not have access to connect to the database server from the host',
	'MSG_LIST_REASONS' => 'This may be due to the following reasons',
	'LBL_MYSQLI_CONNECT_SUPPORT'=>'Mysqli support',
	'LBL_OPEN_SSL'=>'Openssl support',
	'LBL_CURL'=>'Curl support',
    //SalesPlatform.ru begin
    'Install' => 'Install',
    'Vtiger CRM Setup' => 'Install',
    'ERR_NO_UTF8_OR_NO_ALTER_RIGHTS' => 'No utf8 support or no user access to alter database',
    'ru_ru' => 'Русский',
    'en_us' => 'English',
    'es_es' => 'Español',
    'fr_fr' => 'Français',
    'hu_hu' => 'Magyar',
    'pl_pl' => 'Polski',
    'pt_br' => 'Português',
    'LBL_ORGANISATION' => 'Organisation',
        'LBL_CEO' => 'CEO',
        'LBL_VICE_PRESIDENT' => 'Vice president',
        'LBL_SALES_MANAGER' => 'Sales manager',
        'LBL_SALES_PERSON' => 'Sales person',
        'LBL_ADMINISTRATOR' => 'Administrator',
        'LBL_SALES_PROFILE' => 'Sales Profile',
        'LBL_SUPPORT_PROFILE'=> 'Support Profile',
        'LBL_GUEST_PROFILE' => 'Guest Profile',
        'LBL_ADMIN_PROFILE' => 'Admin Profile',
        'LBL_PROFILE_RELATED_TO_SALE' => 'Profile Related to Sales',
        'LBL_PROFILE_RELATED_TO_SUPPORT' => 'Profile Related to Support',
        'LBL_GUEST_PROFILE_FOR_TEST_USERS' => 'Guest Profile for Test Users',
        'holidays_setting' => 'de,en_uk,fr,it,us,',
        'workdays_setting' => '0,1,2,3,4,5,6,',
        'hour_format_setting' => 'am/pm',
        'LBL_TEAM_SELLING' => 'Team Selling',
        'LBL_GROUP_RELATED_TO_SALES' => 'Group Related to Sales',
        'LBL_MARKETING_GROUP' => 'Marketing Group',
        'LBL_GROUP_RELATED_TO_MARKETING_ACTIVITIES' => 'Group Related to Marketing Activities',
        'LBL_SUPPORT_GROUP' => 'Support Group',
        'LBL_GROUP_RELATED_TO_PROVIDING_SUPPORT' => 'Group Related to providing Support to Customers',
    
        'LBL_FREQUENCY_WORKFLOW' => 'Recommended frequency for Workflow is 15 mins',
        'LBL_FREQUENCY_RECURRING_INVOICE' => 'Recommended frequency for RecurringInvoice is 12 hours',
        'LBL_FREQUENCY_SEND_REMINDER' => 'Recommended frequency for SendReminder is 15 mins',
        'LBL_FREQUENCY_SCHEDULE_REPORTS' => 'Recommended frequency for ScheduleReports is 15 mins',
        'LBL_FREQUENCY_MAIL_SCANNER' => 'Recommended frequency for MailScanner is 15 mins',
        'LBL_NOTIFYOWNER_FLAG' => 'Send Email to user when Notifyowner is True',
        'LBL_PORTALUSER_FLAG' => 'Send Email to user when Portal User is True',
        'LBL_POTENTIAL_FLAG' => 'Send Email to users on Potential creation',    
    
        'LBL_REGARDING_ACCOUNT_CREATION_SUBJECT' => 'Regarding Account Creation',
        'LBL_REGARDING_ACCOUNT_CREATION_CONTENT' => 'An Account has been assigned to you on vtigerCRM<br>Details of account are :<br><br>AccountId:<b>$account_no</b><br>AccountName:<b>$accountname</b><br>Rating:<b>$rating</b><br>Industry:<b>$industry</b><br>AccountType:<b>$accounttype</b><br>Description:<b>$description</b><br><br><br>Thank You<br>Admin',
        'LBL_REGARDING_ACCOUNT_CREATION_SUMMARY' => 'An account has been created ',
    
        'LBL_REGARDING_CONTACT_CREATION_SUBJECT' => 'Regarding Contact Creation',
        'LBL_REGARDING_CONTACT_CREATION_CONTENT' => 'An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>Contact Id:<b>$contact_no</b><br>LastName:<b>$lastname</b><br>FirstName:<b>$firstname</b><br>Lead Source:<b>$leadsource</b><br>Department:<b>$department</b><br>Description:<b>$description</b><br><br><br>Thank You<br>Admin',
        'LBL_REGARDING_CONTACT_CREATION_SUMMARY' => 'A contact has been created ',
    
        'LBL_REGARDING_CONTACT_ASSIGNED_SUBJECT' => 'Regarding Contact Assignment',
        'LBL_REGARDING_CONTACT_ASSIGNED_CONTENT' => 'An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>Contact Id:<b>$contact_no</b><br>LastName:<b>$lastname</b><br>FirstName:<b>$firstname</b><br>Lead Source:<b>$leadsource</b><br>Department:<b>$department</b><br>Description:<b>$description</b><br><br><br>And <b>CustomerPortal Login Details</b> is sent to the EmailID :-$email<br><br>Thank You<br>Admin',
        'LBL_REGARDING_CONTACT_ASSIGNED_SUMMARY' => 'An contact has been created ',
    
        'LBL_REGARDING_POTENTIAL_ASSIGNED_SUBJECT' => 'Regarding Potential Assignment',
        'LBL_REGARDING_POTENTIAL_ASSIGNED_CONTENT' => 'An Potential has been assigned to you on vtigerCRM<br>Details of Potential are :<br><br>Potential No:<b>$potential_no</b><br>Potential Name:<b>$potentialname</b><br>Amount:<b>$amount</b><br>Expected Close Date:<b>$closingdate</b><br>Type:<b>$opportunity_type</b><br><br><br>escription :$description<br><br>Thank You<br>Admin',
        'LBL_REGARDING_POTENTIAL_ASSIGNED_SUMMARY' => 'An Potential has been created ',
    
        'LBL_NOTIFICATION_EMAIL_ACTIVITY_SUBJECT' => 'Event :  \$subject',
        'LBL_NOTIFICATION_EMAIL_ACTIVITY_CONTENT' => '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
						.'<b>Activity Notification Details:</b><br/>'
						.'Subject             : $subject<br/>'
						.'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
						.'End date and time   : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
						.'Status              : $eventstatus <br/>'
						.'Priority            : $taskpriority <br/>'
						.'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
                                                .'$(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
						.'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
						.'Location            : $location <br/>'
						.'Description         : $description',
        'LBL_NOTIFICATION_EMAIL_ACTIVITY_SUMMARY' => 'Send Notification Email to Record Owner',
    
        'LBL_NOTIFICATION_EMAIL_TASK_SUBJECT' => 'Task :  \$subject',
        'LBL_NOTIFICATION_EMAIL_TASK_CONTENT' => '$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/>'
						.'<b>Task Notification Details:</b><br/>'
						.'Subject : $subject<br/>'
						.'Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
						.'End date and time   : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>'
						.'Status              : $taskstatus <br/>'
						.'Priority            : $taskpriority <br/>'
						.'Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) '
						.'$(parent_id         : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>'
						.'Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>'
						.'Location            : $location <br/>'
						.'Description         : $description',
        'LBL_NOTIFICATION_EMAIL_TASK_SUMMARY' => 'Send Notification Email to Record Owner',
        'LBL_UPDATE_INVENTORY_PRODUCTS'     =>  'Update Inventory Products On Every Save',
        'LBL_WORKFLOW_FOR_CONTACT_CREATION_AND_MODIFICATION' => 'Workflow for Contact Creation or Modification',
        'LBL_CUSTOMER_PORTAL_LOGIN_DETAILS' => 'Email Customer Portal Login Details',
        'LBL_WORKFLOW_FOR_TICKET_CREATION' => 'Workflow for Ticket Created from Portal',
        'LBL_NOTIFYOWNER_TICKET_CREATION' => 'Notify Record Owner and the Related Contact when Ticket is created from Portal',
        'LBL_WORKFLOW_FOR_TICKET_UPDATE' => 'Workflow for Ticket Updated from Portal',
        'LBL_NOTIFYOWNER_COMMENT_ADDITION' => 'Notify Record Owner when Comment is added to a Ticket from Customer Portal',
        'LBL_WORKFLOW_FOR_TICKET_CHANGE' => 'Workflow for Ticket Change, not from the Portal',
        'LBL_NOTIFYOWNER_TICKET_CHANGE' => 'Notify Record Owner on Ticket Change, which is not done from Portal',
        'LBL_NOTIFYCUSTOMER_TICKET_CHANGE' => 'Notify Related Customer on Ticket Change, which is not done from Portal',
        'LBL_WORKFLOW_FOR_ACTIVITY' => 'Workflow for Events when Send Notification is True',
        'LBL_WORKFLOW_FOR_TASK' => 'Workflow for Calendar Todos when Send Notification is True',
        'LBL_WORKFLOW_FOR_TICKET_COMMENTS' => 'Workflow for comments on Tickets',
        'LBL_WORKFLOW_FORECAST_AMOUNT' => 'Calculate or Update forecast amount',
         
        'LBL_EXIF_SUPPORT'       => 'exif support',
        'LBL_MB_STRING'          => 'Mbstring support',
        'LBL_YOUR_INDUSTRY'      => 'Please let us know your Industry',
        'LBL_CHOOSE_ONE'         => 'Choose one...',
        'LBL_ACCOUNTING'         => 'Accounting',
        'LBL_ADVERTISING'        => 'Advertising',
        'LBL_AGRICULTURE'        => 'Agriculture',
        'LBL_APPAREL_ACCESSORIES'=> 'Apparel &amp; Accessories',
        'LBL_AUTOMOTIVE'         => 'Automotive',
        'LBL_BANKING_FINANCIAL_SERVICES'        => 'Banking &amp; Financial Services',
        'LBL_BIOTECHNOLOGY'      => 'Biotechnology',
        'LBL_CALL_CENTERS'       => 'Call Centers',
        'LBL_CAREERS_EMPLOYMENT' => 'Careers/Employment',
        'LBL_CHEMICAL'           => 'Chemical',
        'LBL_COMPUTER_HARDWARE'  => 'Computer Hardware',
        'LBL_COMPUTER_SOFTWARE'  => 'Computer Software',
        'LBL_CONSULTING'         => 'Consulting',
        'LBL_CONSTRUCTION'       => 'Construction',
        'LBL_EDUCATION'          => 'Education',
        'LBL_ENERGY_SERVICES'    => 'Energy Services',
        'LBL_ENGINEERING'        => 'Engineering',
        'LBL_ENTERTAINMENT'      => 'Entertainment',
        'LBL_FINANCIAL'          => 'Financial',
        'LBL_FOOD'               => 'Food &amp; Food Service',
        'LBL_GOVERNMENT'         => 'Government',
        'LBL_HEALTH_CARE'        => 'Health care',
        'LBL_INSURANCE'          => 'Insurance',
        'LBL_LEGAL'              => 'Legal',
        'LBL_LOGISTICS'          => 'Logistics',
        'LBL_MANUFACTURING'      => 'Manufacturing',
        'LBL_MEDIA_PRODUCTION'   => 'Media &amp; Production',
        'LBL_NON_PROFIT'         => 'Non-profit',
        'LBL_PHARMACEUTICAL'     => 'Pharmaceutical',
        'LBL_REAL_ESTATE'        => 'Real Estate',
        'LBL_RENTAL'             => 'Rental',
        'LBL_RETAIL_WHOLESALE'   => 'Retail &amp; Wholesale',
        'LBL_SECURITY'           => 'Security',
        'LBL_SERVICE'            => 'Service',
        'LBL_SPORTS'             => 'Sports',
        'LBL_TELECOMMUNICATIONS' => 'Telecommunications',
        'LBL_TRANSPORTATION'     => 'Transportation',
        'LBL_TRAVEL_TOURISM'     => 'Travel &amp; Tourism',
        'LBL_UTILITIES'          => 'Utilities',
        'LBL_OTHER'              => 'Other',
    
        'LBL_WE_COLLECT_INFORMATION'         => 'We collect anonymous information (Country, OS) 
                                                to help us improve future versions of Vtiger. 
                                                Data about how CRM is used and where it is being used helps 
                                                us identify the areas in the product that need to be enhanced. 
                                                We use this data to improve your experience with Vtiger. 
                                                None of the data collected here can be linked back to an individual.',
    
        'LBL_WHAT_WOULD_YOU_LIKE'    => 'What would you like to use Vtiger CRM for?',  
        'LBL_VIEW_MODULES'           => 'View modules',
        'LBL_ENABLED_FOR_THIS_FEATURE'           => 'These Modules will be enabled for this feature',
        'LBL_NOTE'                   => 'Note',
        'LBL_NOTE_ABOUT_MODULE_MANAGER'          => 'You can Enable/Disable modules from module manager later',
        'LBL_PDF_INVOICE_BODY'  => ',\'Invoice\',\'Invoice\',\'{header}\n\n<p style="font-weight: bold; text-decoration: underline">{$orgName}</p>\n\n<p style="font-weight: bold">Address: {$orgBillingAddress}, phone: {$orgPhone}</p>\n\n\n<br/>\n<h1 style="text-align: center">INVOICE № {$invoice_no} from {$invoice_invoicedate}</h1>\n<br/><br/>\n<table border="0">\n<tr>\n<td width="100">Payer:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n<tr>\n<td width="100">Consignee:</td><td width="450"><span style="font-weight: bold">{$account_accountname}</span></td>\n</tr>\n</table>\n\n{/header}\n\n{table_head}\n<table border="1" style="font-size: 8pt" cellpadding="2">\n    <tr style="text-align: center; font-weight: bold">\n	<td width="30">№</td>\n      <td width="260">Name of<br/>product</td>\n      <td width="65">Unit</td>\n      <td width="35">Quantity</td>\n	<td width="70">Price</td>\n	<td width="70">Amount</td>\n	</tr>\n{/table_head}\n\n{table_row}\n    <tr>\n	<td width="30">{$productNumber}</td>\n      <td width="260">{$productName} {$productComment}</td>\n	<td width="65" style="text-align: center">{$productUnits}</td>\n	<td width="35" style="text-align: right">{$productQuantity}</td>\n	<td width="70" style="text-align: right">{$productPriceWithTax}</td>\n	<td width="70" style="text-align: right">{$productTotal}</td>\n    </tr>\n{/table_row}\n\n{summary}\n</table>\n<table border="0" style="font-size: 8pt;font-weight: bold">\n    <tr>\n      <td width="460">\n        <table border="0" cellpadding="2">\n          <tr><td width="460" style="text-align: right">In total:</td></tr>\n          <tr><td width="460" style="text-align: right">Including tax:</td></tr>\n          <tr><td width="460" style="text-align: right">Total to pay:</td></tr>\n        </table>\n      </td>\n      <td width="70">\n        <table border="1" cellpadding="2">\n          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryTax}</td></tr>\n          <tr><td width="70" style="text-align: right">{$summaryGrandTotal}</td></tr>\n        </table>\n      </td>\n  </tr>\n</table>\n\n<p>\nTotal items {$summaryTotalItems}, to amount {$summaryGrandTotal}<br/>\n<span style="font-weight: bold">{$summaryGrandTotalLiteral}</span>\n</p>\n\n{/summary}\n\n{ending}\n<br/>\n    <p>Directior  __________________ ( {$orgDirector} ) <br/>\n    <br/>\n    General bookkeeper  __________________ ( {$orgBookkeeper} )\n    </p>\n{/ending}\',110,50,\'P\'',
        'LBL_SEND_NOTIFICATION_TO_INVITED_USERS' => 'Send e-mail to invited users',
        'LBL_EVENT' => 'Event',
        'LBL_EVENT_DETAILS' => 'Event Details',
        'LBL_EVENT_NAME' => 'Subject',
        'LBL_START_DATETIME' => 'Start Date & Time',
        'LBL_END_DATETIME' => 'End Date & Time',
        'LBL_STATUS' => 'Status',
        'LBL_PRIORITY' => 'Priority',
        'LBL_RELATED_WITH' => 'Related To',
        'LBL_CONTACTS' => 'Contacts',
        'LBL_LOCATION' => 'Location',
        'LBL_DESCRIPTION' => 'Description',
        'LBL_SEND_EMAIL_TO_ASSIGNED_USER' => 'Send e-mail task assigned user',
        'LBL_TASK' => 'Task',
        'LBL_TASK_DETAILS' => 'Task Details',
        'LBL_TASK_NAME' => 'Subject',
        'LBL_TICKET_CREATION_FROM_PORTAL' =>'Ticket Creation From Portal : Send Email to Record Owner and Contact',
        'LBL_NOTIFY_RECORD_OWNER_AFTER_TICKET_CREATION' => 'Notify Record Owner when Ticket is created from Portal',
        'LBL_SEND_EMAIL_TO_CONTACT_ON_TICKET_UPDATE' => 'Send Email to Contact on Ticket Update',
        'LBL_UP_INV_PRODUCTS' => 'Update Inventory Products',
        'LBL_COMMECT_ADDED_FROM_PORTAL_SEND_EMAIL' =>'Comment Added From Portal : Send Email to Record Owner',
        'LBL_SEND_EMAIL_TO_CONTACT_WHERE_CONTACT_NOT_PORTAL_USER' => 'Comment Added From CRM : Send Email to Contact, where Contact is not a Portal User',
        'LBL_COMMENT_ADDED_FROM_CRM_SEND_EMAIL_TO_PORTAL_CONTACT_USER' => 'Comment Added From CRM : Send Email to Contact, where Contact is Portal User',
        'LBL_SEND_EMAIL_TO_RECORD_OWNER_ON_TICKET_UPDATE' => 'Send Email to Record Owner on Ticket Update',
        'TICKET_CREATION_FROM_CRM_SEND_EMAIL_TO_OWNER' => 'Ticket Creation From CRM : Send Email to Record Owner',
        'LBL_EMAIL_TO_ORGANIZATION_ON_TICKET_UPDATE' => 'Send Email to Organization on Ticket Update',
        'LBL_EMAIL_ORGANIZATION_ON_TICKET_CREATE' => 'Ticket Creation From CRM : Send Email to Organization',
        'LBL_TICKET_CREATION_SEND_EMAIL_TO_CONTACT' => 'Ticket Creation From CRM : Send Email to Contact',
        'LBL_COMMENT_ADDED_FROM_CRM_SEND_EMAL_ORG' => 'Comment Added From CRM : Send Email to Organization',
        'LBL_BACK' => 'Back',
        'NOT RECOMMENDED' => 'Not recommended parameters',
    //SalesPlatform.ru end
        //SalesPlatform.ru begin compatibility with mysql 5.7.5+
        'LBL_MYSQL_RECOMMENDED_SETTINGS' => 'Recommended MySQL Settings'
        //SalesPlatform.ru end
);
//SalesPlatform.ru begin
$jsLanguageStrings = array(
	'JS_PHP_INCORRECT_VALUE'     => 'Some of the PHP Settings do not meet the recommended values. This might affect some of the features of vtiger CRM. Are you sure, you want to proceed?',
	'JS_PASSWORD_MISMATCH'       => 'Please re-enter passwords.  The "Password" and "Re-type password" values do not match.',
        'JS_INCORRECT_EMAIL'         => 'Warning! Invalid email address.',
        'JS_EMPTY_MAJOR_FIELDS'      => 'Warning! Required fields missing values.',
        'JS_PLEASE_RESOLVE_ERROR'    => 'Please resolve the error before proceeding with the installation',
        'JS_YOUR_INDUSTRY'           => 'Please let us know your Industry',
        //SalesPlatform.ru begin compatibility with mysql 5.7.5+
        'JS_SQL_MODE_INCORRECT_VALUE'   => 'Disable strict mode controls( SET GLOBAL sql_mode = \'\' ). This might affect some of the features of vtiger CRM. Are you sure, you want to proceed? '
        //SalesPlatform.ru end
);
//SalesPlatform.ru end
