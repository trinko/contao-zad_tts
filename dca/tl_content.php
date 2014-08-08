<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   zad_tts
 * @author    Antonello Dessì
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @copyright Antonello Dessì 2014
 */


/**
 * Table tl_content
 */

// Configuration
$GLOBALS['TL_DCA']['tl_content']['config']['ondelete_callback'][] =
  array('tl_content_zad_tts', 'deleteNewsElement');
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] =
  array('tl_content_zad_tts', 'submitNewsElement');


/**
 * Class tl_content_zad_tts
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright Antonello Dessì 2014
 * @author    Antonello Dessì
 * @package   zad_tts
 */
class tl_content_zad_tts extends Backend {

	/**
	 * Unset "updated" flag for the news owner of this content
	 *
	 * @param \DataContainer $dc  The data container of the table
	 */
	public function deleteNewsElement($dc) {
    if ($this->isUpdated($dc)) {
      // unset "updated" flag
  		$this->Database->prepare("UPDATE tl_news SET zad_tts_updated=? WHERE id=?")
  				           ->execute('', $dc->activeRecord->pid);
    }
	}

	/**
	 * Unset "updated" flag for the news owner of this content
	 *
	 * @param \DataContainer $dc  The data container of the table
	 */
	public function submitNewsElement($dc) {
    if ($this->isUpdated($dc)) {
      // unset "updated" flag
  		$this->Database->prepare("UPDATE tl_news SET zad_tts_updated=? WHERE id=?")
  				           ->execute('', $dc->activeRecord->pid);
    }
	}

	/**
	 * Return True if the news owner of this content is updated
	 *
	 * @param \DataContainer $dc  The data container of the table
	 *
	 * @return bool  True if the news is updated, False otherwise
	 */
	protected function isUpdated($dc) {
    if ($dc->activeRecord && $dc->activeRecord->ptable == 'tl_news' && $dc->activeRecord->type == 'text') {
      // check if "updated" flag is set
      $rs = $this->Database->prepare("SELECT zad_tts_updated FROM tl_news WHERE id=?")
				                   ->execute($dc->activeRecord->pid);
      if ($rs && $rs->zad_tts_updated > 0) {
        // news is updated
        return true;
      }
    }
    // news is outdated
    return false;
	}

}

