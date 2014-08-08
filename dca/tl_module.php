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
 * Table tl_module
 */

// Configuration
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] =
  array('tl_module_zad_tts', 'configure');

// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['zad_tts_newsreader']  =
  str_replace('{config_legend},news_archives;',
              '{config_legend},news_archives,zad_tts_player,zad_tts_download;',
              $GLOBALS['TL_DCA']['tl_module']['palettes']['newsreader']);

// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['zad_tts_player'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['zad_tts_player'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'default'                 => '',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['zad_tts_download'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['zad_tts_download'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'default'                 => '',
	'eval'                    => array('tl_class'=>'w50'),
	'sql'                     => "char(1) NOT NULL default ''"
);


/**
 * Class tl_module_zad_tts
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright Antonello Dessì 2014
 * @author    Antonello Dessì
 * @package   zad_tts
 */
class tl_module_zad_tts extends Backend {

	/**
	 * Dynamic fields configuration for the module
	 *
	 * @param \DataContainer $dc  The data container of the table
	 */
	public function configure($dc) {
		if (Input::get('act') != 'edit' && Input::get('act') != 'show') {
      // not in edit/show mode
			return;
		}
		$module = ModuleModel::findByPk($dc->id);
    if ($module && $module->type == 'zad_tts_newsreader') {
      // field "news_archives" configuration
      $GLOBALS['TL_DCA']['tl_module']['fields']['news_archives']['label'] = &$GLOBALS['TL_LANG']['tl_module']['zad_tts_newsarchives'];
      $GLOBALS['TL_DCA']['tl_module']['fields']['news_archives']['options_callback'] = array('tl_module_zad_tts', 'getNewsArchives');
      // field "news_template" configuration
      $GLOBALS['TL_DCA']['tl_module']['fields']['news_template']['options_callback'] = array('tl_module_zad_tts', 'getNewsTemplates');
    }
  }

	/**
	 * Get all audio news archives and return them as array
	 *
	 * @return array  The list of news archives.
	 */
	public function getNewsArchives() {
		$list = array();
    // get only news archives with audio enabled
		$archives = $this->Database->prepare("SELECT id,title FROM tl_news_archive WHERE zad_tts_active=? ORDER BY title")
  				           ->execute('1');
		while ($archives->next()) {
			$list[$archives->id] = $archives->title;
		}
    // return audio news archives list
		return $list;
	}

	/**
	 * Return all zad_tts templates as array
	 *
	 * @return array  List of templates
	 */
	public function getNewsTemplates() {
		return $this->getTemplateGroup('zad_tts_news_');
	}

}

