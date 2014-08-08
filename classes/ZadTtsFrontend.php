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
 * Class ZadTtsFrontend
 *
 * @copyright  Antonello Dessì 2014
 * @author     Antonello Dessì
 * @package    zad_tts
 */
class ZadTtsFrontend extends \Frontend {

	/**
	 * Contao hook executed before parsing news template.
	 *
	 * @param \FrontendTemplate $template  Template object used to display news items.
	 * @param array $row  Table row with current news data.
	 * @param \ModuleZadTtsNewsreader $module  The current module object.
	 */
  public function parseArticles($template, $row, $module) {
    $template->zad_tts_player = false;
    $template->zad_tts_download = false;
    // check if audio file is updated
    if ($row['zad_tts_updated']) {
      // audio file updated, check if plugin is enabled
		  $rs = $this->Database->prepare("SELECT zad_tts_active,zad_tts_dir FROM tl_news_archive WHERE id=?")
				         ->execute($row['pid']);
      if ($rs && $rs->zad_tts_active) {
        // plugin enabled, check if file exists
		    $folder = \FilesModel::findByUuid($rs->zad_tts_dir);
        $file = $folder->path . '/news_' . $row['id'] . '.mp3';
        if (file_exists(TL_ROOT . '/' . $file)) {
          // audio file exists
          if ($module->zad_tts_player) {
            // show player
            $template->zad_tts_player = true;
            $template->zad_tts_media = \Environment::get('base') . $file;
            $template->zad_tts_swf = \Environment::get('base') . 'system/modules/zad_tts/vendor/jplayer';
            $template->zad_tts_msg_play = $GLOBALS['TL_LANG']['zad_tts']['msg_play'];
            $template->zad_tts_msg_pause = $GLOBALS['TL_LANG']['zad_tts']['msg_pause'];
            $template->zad_tts_msg_stop = $GLOBALS['TL_LANG']['zad_tts']['msg_stop'];
            $template->zad_tts_msg_mute = $GLOBALS['TL_LANG']['zad_tts']['msg_mute'];
            $template->zad_tts_msg_unmute = $GLOBALS['TL_LANG']['zad_tts']['msg_unmute'];
            $template->zad_tts_msg_maxvolume = $GLOBALS['TL_LANG']['zad_tts']['msg_maxvolume'];
            $template->zad_tts_msg_errtitle = $GLOBALS['TL_LANG']['zad_tts']['msg_errtitle'];
            $template->zad_tts_msg_errdesc = $GLOBALS['TL_LANG']['zad_tts']['msg_errdesc'];
            $template->zad_tts_msg_audiolink = $GLOBALS['TL_LANG']['zad_tts']['msg_audiolink'];
            $template->zad_tts_msg_closebox = $GLOBALS['TL_LANG']['zad_tts']['msg_closebox'];
          }
          if ($module->zad_tts_download) {
            // show download
            $template->zad_tts_download = true;
		        $href = preg_replace('/(&(amp;)?|\?)file=[^&]+/', '', \Environment::get('request'));
		        $href .= ((strpos($href, '?') !== false) ? '&amp;' : '?') . 'file=' . $row['id'];
            $template->zad_tts_media_dl = $href;
            $template->zad_tts_msg_download = addslashes($GLOBALS['TL_LANG']['zad_tts']['msg_download']);
          }
        }
      }
    }
  }

}

