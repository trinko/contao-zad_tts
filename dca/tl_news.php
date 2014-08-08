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
 * Table tl_news
 */

// Configuration
$GLOBALS['TL_DCA']['tl_news']['config']['ondelete_callback'][] =
  array('tl_news_zad_tts', 'deleteNews');
$GLOBALS['TL_DCA']['tl_news']['config']['onsubmit_callback'][] =
  array('tl_news_zad_tts', 'submitNews');

// Listing
$GLOBALS['TL_DCA']['tl_news']['list']['operations']['zad_tts_update'] = array(
	'label'               => '',
	'href'                => 'key=zad_tts',
	'icon'                => 'system/modules/zad_tts/assets/outdated.gif',
	'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.displayBox(\''.$GLOBALS['TL_LANG']['tl_news']['zad_tts_waiting'].'\')"',
  'button_callback'     => array('tl_news_zad_tts', 'iconUpdate')
);

// Fields
$GLOBALS['TL_DCA']['tl_news']['fields']['zad_tts_updated'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news']['zad_tts_updated'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'default'                 => '',
	'sql'                     => "char(1) NOT NULL default ''"
);


/**
 * Class tl_news_zad_tts
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright Antonello Dessì 2014
 * @author    Antonello Dessì
 * @package   zad_tts
 */
class tl_news_zad_tts extends Backend {

	/**
	 * Archive of the news
	 *
	 * @var Object $zad_tts_archive  Table row of the news archive
	 */
	protected $zad_tts_archive = null;


	/**
	 * Return the "update" button
	 *
	 * @param array $row  The table row
	 * @param string $href  Url for the button
	 * @param string $label  Label text for the button
	 * @param string $title  Title text for the button
	 * @param string $icon  Icon name for the button
	 * @param string $attributes  Other attributes for the button
	 *
	 * @return string  Html text for the button
	 */
	public function iconUpdate($row, $href, $label, $title, $icon, $attributes) {
    if (!$this->isEnabled($row['pid'])) {
      // plugin disabled
      return '';
    }
		$folder = \FilesModel::findByUuid($this->zad_tts_archive->zad_tts_dir);
    if ($row['zad_tts_updated'] && file_exists(TL_ROOT . '/' . $folder->path . '/news_' . $row['id'] . '.mp3')) {
      // create icon for up to date audio
      $icon = 'system/modules/zad_tts/assets/updated.gif';
      $label = $GLOBALS['TL_LANG']['tl_news']['zad_tts_but_updated'][0];
      $title = $GLOBALS['TL_LANG']['tl_news']['zad_tts_but_updated'][1];
      return Controller::generateImage($icon, $label, 'title="'.$title.'"');
    }
    // create button for out of date audio
    $icon = 'system/modules/zad_tts/assets/outdated.gif';
    $label = $GLOBALS['TL_LANG']['tl_news']['zad_tts_but_outdated'][0];
    $title = $GLOBALS['TL_LANG']['tl_news']['zad_tts_but_outdated'][1];
		$href .= '&amp;Zid='.$row['id'];
		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Controller::generateImage($icon, $label).'</a> ';
	}

	/**
	 * Remove audio file of this news
	 *
	 * @param \DataContainer $dc  The data container of the table
	 */
	public function deleteNews($dc) {
    if ($dc->activeRecord && $this->isEnabled($dc->activeRecord->pid)) {
		  $folder = \FilesModel::findByUuid($this->zad_tts_archive->zad_tts_dir);
      $file = TL_ROOT . '/' . $folder->path . '/news_' . $dc->id . '.mp3';
      if (file_exists($file)) {
        // remove audio file
        unlink($file);
      }
    }
	}

	/**
	 * Unset "updated" flag for this news
	 *
	 * @param \DataContainer $dc  The data container of the table
	 */
	public function submitNews($dc) {
    if ($dc->activeRecord && $this->isEnabled($dc->activeRecord->pid)) {
      // unset "updated" flag
  		$this->Database->prepare("UPDATE tl_news SET zad_tts_updated=? WHERE id=?")
  				           ->execute('', $dc->id);
    }
	}

	/**
	 * Return True if "zad_tts" is enabled for this news archive
	 *
	 * @param int $id  The news archive identifier
	 *
	 * @return bool  True if "zad_tts" is enabled, False otherwise
	 */
	protected function isEnabled($id) {
    // read news archive configuration
		$this->zad_tts_archive = $this->Database->prepare("SELECT * FROM tl_news_archive WHERE id=?")
				                                    ->execute($id);
    return ($this->zad_tts_archive && $this->zad_tts_archive->zad_tts_active > 0);
	}

}

