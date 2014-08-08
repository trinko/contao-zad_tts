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
 * Table tl_news_archive
 */

// Palettes
$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['__selector__'][] = 'zad_tts_active';
$GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default'] =
  str_replace('{protected_legend:hide}',
              '{zad_tts_legend},zad_tts_active;{protected_legend:hide}',
              $GLOBALS['TL_DCA']['tl_news_archive']['palettes']['default']);

// Subpalettes
$GLOBALS['TL_DCA']['tl_news_archive']['subpalettes']['zad_tts_active'] = 'zad_tts_dir';

// Fields
$GLOBALS['TL_DCA']['tl_news_archive']['fields']['zad_tts_active'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news_archive']['zad_tts_active'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'default'                 => '',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_news_archive']['fields']['zad_tts_dir'] = array(
	'label'                   => &$GLOBALS['TL_LANG']['tl_news_archive']['zad_tts_dir'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio', 'files'=>false, 'tl_class'=>'clr'),
	'sql'                     => "binary(16) NULL"
);

