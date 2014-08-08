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
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['news']['zad_tts'] = array('ZadTtsBackend', 'update');


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['news']['zad_tts_newsreader'] = 'ModuleZadTtsNewsreader';


/**
 * HOOKS
 */
$GLOBALS['TL_HOOKS']['parseArticles'][] = array('ZadTtsFrontend', 'parseArticles');

