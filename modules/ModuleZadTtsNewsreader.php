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
 * Namespace
 */
namespace zad_tts;


/**
 * Class ModuleZadTtsNewsreader
 *
 * @copyright  Antonello Dessì 2014
 * @author     Antonello Dessì
 * @package    zad_tts
 */
class ModuleZadTtsNewsreader extends \ModuleNewsReader {

	/**
	 * Generate the HTML code
	 *
	 * @return string  The Html code
	 */
	public function generate() {
		$file = intval(\Input::get('file', true));
    // check file parameter
		if ($file > 0) {
		  $rs = $this->Database->prepare("SELECT a.zad_tts_active,a.zad_tts_dir FROM tl_news_archive AS a,tl_news AS n WHERE n.pid=a.id AND n.id=?")
                           ->execute($file);
      if ($rs && $rs->zad_tts_active && ($folder = \FilesModel::findByUuid($rs->zad_tts_dir))) {
        // send the file to the browser
  			\Controller::sendFileToBrowser($folder->path . '/news_' . $file . '.mp3');
      }
		}
    // execute news_reader::generate() method
    return parent::generate();
  }

	/**
	 * Generate the module
	 */
	protected function compile() {
	  // add scripts
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/zad_tts/vendor/jplayer/jquery.jplayer.min.js';
	  // add CSS styles
    $GLOBALS['TL_CSS'][] = 'system/modules/zad_tts/assets/zad_tts.css';
    // execute news_reader::compile() method
    parent::compile();
  }

}

