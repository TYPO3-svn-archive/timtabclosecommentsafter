<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

//get EXT path
$PATH_closeCommentsAfter = t3lib_extMgm::extPath('timtab_closecommentsafter');

if (TYPO3_MODE == 'FE')	{
	require_once($PATH_closeCommentsAfter.'class.tx_timtabclosecommentsafter_close.php');
}

//registering for several hooks
$TYPO3_CONF_VARS['EXTCONF']['ve_guestbook']['preEntryInsertHook'][] = 'tx_timtabclosecommentsafter_close';

?>