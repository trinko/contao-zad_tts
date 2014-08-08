<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Zad_tts
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'zad_tts',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'zad_tts\ZadTtsBackend'          => 'system/modules/zad_tts/classes/ZadTtsBackend.php',
	'zad_tts\ZadTtsFrontend'         => 'system/modules/zad_tts/classes/ZadTtsFrontend.php',

	// Modules
	'zad_tts\ModuleZadTtsNewsreader' => 'system/modules/zad_tts/modules/ModuleZadTtsNewsreader.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'zad_tts_news_full' => 'system/modules/zad_tts/templates',
));
