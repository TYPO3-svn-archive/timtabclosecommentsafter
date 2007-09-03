<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Ingo Renner (typo3@ingo-renner.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * class.tx_timtabclosecommentsafter_close.php
 *
 * checks whether an attempt is made to write a comment after a configured timeout
 * if that is the case the close comments flag for the post is set and the creation
 * of the comment is denied
 *
 * @author Ingo Renner <typo3@ingo-renner.com>
 */

class tx_timtabclosecommentsafter_close {
	
	/**
	 * checks whether the time to write comments for a post is up and closes 
	 * comments if that is the case
	 * 
	 * TODO also hook into TIMTAB to close pings, too
	 *
	 * @param	array	comment record from ve_guestbook
	 * @param	tx_veguestbook_pi1	parent ve_guestbook object
	 * @return	array	ve_guestbook comment
	 */
	function preEntryInsertProcessor($comment, $pObj) {
		
		if($comment['uid_tt_news']) {
			$daysUntilClose = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_timtab.']['comments.']['closeAfter'];
			
			$post = $this->getPost($comment['uid_tt_news']);
			
			$timeToWait = $daysUntilClose * 86400;
			if($daysUntilClose > 0 && (($post['datetime'] + $timeToWait) < time())) {
					// time is up, close comments
				$this->closeComments($comment['uid_tt_news']);
				$comment = array();	// unset
			}
		}
		
		return $comment;
	}
	
	/**
	 * gets the tt_news record with the given ID
	 * 
	 * TODO move this to tx_timtab_lib
	 *
	 * @param	integer	tt_news record uid
	 * @return	array	tt_news record
	 */
	function getPost($postId) {
		$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tt_news',
			'uid = '.$postId
		);
		
		return $record[0];
	}
	
	/**
	 * closes comments for a given post
	 *
	 * @param	integer	tt_news record uid
	 */
	function closeComments($postId) {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tt_news',
			'uid = '.$postId,
			array('tx_timtab_comments_allowed' => 0)
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_closecommentsafter/class.tx_timtabclosecommentsafter_close.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab_closecommentsafter/class.tx_timtabclosecommentsafter_close.php']);
}

?>