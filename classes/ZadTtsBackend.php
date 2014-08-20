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
 * Class ZadTtsBackend
 *
 * @copyright  Antonello Dessì 2014
 * @author     Antonello Dessì
 * @package    zad_tts
 */
class ZadTtsBackend extends \Backend {

	/**
	 * MP3 file handle
	 *
	 * @var mixed $mp3_file  The file handle for MP3 data
	 */
	protected $mp3_file = null;

	/**
	 * Default language
	 *
	 * @var string $language  The language identifier
	 */
	protected $language = null;


	/**
	 * MP3 pause of 100 ms
	 *
	 * @var mixed $mp3_pause  The binary MP3 data for a 100ms pause
	 */
	protected $mp3_pause = null;


	/**
	 * Create a new audio file for the text of the news
	 *
	 * @param \DataContainer $dc  The data container of the table
	 */
	public function update($dc) {
    // initialize
    $this->mp3_file = null;
    $this->language = $GLOBALS['TL_LANGUAGE'];
    $this->mp3_pause = pack('H*', 'fff348c4341b72964800c056b84e2f507c2897992e6a4a6ab6370250071752f691e79ab921dc6e712386cf6c3a4d5917f2d877fc7dfffff77fcf134ea4adef1cdffa3fd47b9eae30c038a13ab310be85aaaca70fd858d0a8497ef2cecf42409074788c8b43f926e78a75ea5aa6ba736f26a67f655d6219e153d9a18ea95a866283a15429a0e104168c43961ae4a9ea2ffff348c4491cc9b23000ca10946724d0d52120a05516095cf2475e59314167b89a5ffdd43aad5f63368a1fe2fcd994393cf58bf6edbef5d58499341e6f08cf379d013adc109495c91c5f28b5a94d00eaa14686ab0d6aaf42898cb0eedfb330137fb1afc66d49b528c7ff1b9ff4bf5666666a266a1f50341dec2c0c808f1dac153a583846223dea0ec441d6c153a0b2dcfff348c4581cf18e0c00c8c69459f2d2c0c82b3d9d06a74ed40d5631f2a0a82b89435e9eb0d2e54ead20a3e890932232501332404fcf73e1128a3e5200b808038d943a70ea8ba8bce17fffea5195d5d5d2c9a5654792cb1d2c97ffeab2cb2cb594103040c13161616161767ffff15151616161617151515156ffac54545451bffc54585858585c54545454585858585dfff348c46719b16104007a46944c414d45332e39382e3255555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555555');
    // get news identifier
    $news_id = intval($_GET['Zid']);
    // get all news data
    $news = $this->Database->prepare("SELECT a.zad_tts_dir,n.headline,n.teaser FROM tl_news AS n,tl_news_archive AS a WHERE a.id=n.pid AND n.id=?")
                           ->execute($news_id);
    $content = $this->Database->prepare("SELECT headline,text FROM tl_content WHERE pid=? and ptable='tl_news' and type='text' ORDER BY sorting")
                              ->execute($news_id);
    // create new mp3 file
    $folder = \FilesModel::findByUuid($news->zad_tts_dir);
    $filename = TL_ROOT . '/' . $folder->path . '/news_' . $news_id . '.mp3';
    $this->mp3_file = fopen($filename, 'w');
    if (!$this->mp3_file) {
      // fatal error
      $this->log('Unable to create MP3 file "'.$filename.'"', __METHOD__, TL_ERROR);
      $this->redirect($this->getReferer());
      return;
    }
    // append news title
    if (!$this->appendText($news->headline, 'title')) {
      // fatal error
      $this->redirect($this->getReferer());
      return;
    }
    // check news content
    if ($content->numRows == 0) {
      // no content, append news teaser
      if (!$this->appendText($news->teaser, 'text')) {
        // fatal error
        $this->redirect($this->getReferer());
        return;
      }
    } else {
      // append all contents
      while ($content->next()) {
        // check headline
        if ($content->headline) {
          $headline = unserialize($content->headline);
          if ($headline['value']) {
            // append headline
            if (!$this->appendText($headline['value'], 'headline')) {
              // fatal error
              $this->redirect($this->getReferer());
              return;
            }
          }
        }
        // append content news
        if (!$this->appendText($content->text, 'text')) {
          // fatal error
          $this->redirect($this->getReferer());
          return;
        }
      }
    }
    // close mp3 file
    fclose($this->mp3_file);
    // set "update" flag
		$this->Database->prepare("UPDATE tl_news SET zad_tts_updated=? WHERE id=?")
				           ->execute('1', $news_id);
    // redirect to news management page
    $this->redirect($this->getReferer());
  }

	/**
	 * Convert a text to speech and append it to a MP3 file
	 *
	 * @param string $text  Text to be converted to audio file
	 * @param string $type  Type of the text
	 *
	 * @return bool  True if everithing is ok, False otherwise
	 */
  protected function appendText($text, $type) {
    // clean text
    $text = $this->cleanText($text);
    if (!strlen($text)) {
      return true;
    }
    // split text according to punctuaction:
    //    ?!;     in any position
    //    :       avoiding time format (i.e. 8:30)
    //    .       avoiding number format (i.e. 12.30) and acronyms (i.e. F.I.G.C.)
    $sentences = preg_split('#([\?!;]|(?<!\d):|:(?!\d)|(?<=\d)\.(?!\d)|(?<!\d)\.(?=\d)|(?<=\s)\.|\.(?=\s))#si', $text, null, PREG_SPLIT_DELIM_CAPTURE);
    // create TTS for each sentence
    for ($idx = 0; $idx < count($sentences); $idx++) {
      $s = trim($sentences[$idx]);
      if (strlen($s) == 0 || ctype_punct($s)) {
        // nothing to do, skip
        continue;
      }
      $idx++;
      if ($idx < count($sentences)) {
        // add delimiter
        $s .= $sentences[$idx];
      } else {
        // add a full stop
        $s .= '.';
      }
      // append sentence to MP3 file
      if (!$this->appendSentence($s)) {
        // fatal error
        return false;
      }
    }
    // add a pause
    switch ($type) {
      case 'title':
        $pause = 1000;
        break;
      case 'headline':
        $pause = 800;
        break;
      default:
        $pause = 500;
        break;
    }
    return $this->appendPause($pause);
  }

	/**
	 * Clean text from HTML tags
	 *
	 * @param string $text  Html text
	 *
	 * @return string  Cleaned text
	 */
  protected function cleanText($text) {
    // strip inline scripts
    $text = preg_replace('#<script\b[^>]*>.*?</script>#si', ' ', $text);
    // strip inline styles
    $text = preg_replace('#<style\b[^>]*>.*?</style>#si', ' ', $text);
    // strip comments
    $text = preg_replace('#<!--.*?-->#si', ' ', $text);
    // add a full stop at the end of headline/paragraph/table-row
    $text = preg_replace('#</(h[1-6]|p|tr)>#si', '.', $text);
    $text .= (substr($text, -1) != '.') ? '.' : '';
    // add a semi-colon at the end of list-item/table-column
    $text = preg_replace('#</(li|td)>#si', ';', $text);
    // strip all HTML tags
    $text = preg_replace('#<[^>]*>#si', ' ', $text);
    // remove unuseful spaces
    $text = trim(preg_replace('#(\s|&nbsp;|\[nbsp\])+#si', ' ', $text));
    $text = preg_replace('/ ([\.,;:!\?])/', '$1', $text);
    // remove HTML entities
    $text = html_entity_decode($text);
    // remove unuseful text
    if (strlen($text) > 0 && ctype_punct($text)) {
      $text = '';
    }
    // return cleaned text
    return $text;
  }

	/**
	 * Convert a sentence to speech and append it to a MP3 file
	 *
	 * @param string $sentence  Sentence to be converted to audio file
	 *
	 * @return bool  True if everithing is ok, False otherwise
	 */
  protected function appendSentence($sentence) {
    // split sentence according to puntuaction:
    //    ()    in any position
    //    ,     avoiding number formats (i.e. 12,30)
    $parts = preg_split('#([\(\)]|(?<!\d),|,(?!\d))#si', $sentence, null, PREG_SPLIT_DELIM_CAPTURE);
    // create TTS for each text part
    $tts = array();
    for ($idx = 0; $idx < count($parts); $idx++) {
      $s = trim($parts[$idx]);
      if (strlen($s) == 0 || ctype_punct($s)) {
        // nothing to do, skip
        continue;
      }
      $idx++;
      if ($idx < count($parts)) {
        // add delimiter
        $s .= $parts[$idx];
      } elseif (!ctype_punct(substr($s, -1))) {
        // add a comma
        $s .= ',';
      }
      // split text to limit length
      while (strlen($s) > 100) {
        $cnt = 99;
        while ($cnt > 0 && $s{$cnt} != ' ') {
          $cnt--;
        }
        $cnt = ($cnt == 0) ? 99 : $cnt;
        // add text part
        $tts[] = substr($s, 0, $cnt);
        $s = substr($s, $cnt + 1);
      }
      if (strlen($s) > 0) {
        // add last text part
        $tts[] = $s;
      }
    }
    // append all text part to MP3 file
    if (!$this->appendAudio($tts)) {
      // fatal error
      return false;
    }
    // add pause
    return $this->appendPause(100);
  }

	/**
	 * Convert a list of text parts to speech and append it to a MP3 file
	 *
	 * @param array $list  List of text parts to be converted to audio file
	 *
	 * @return bool  True if everithing is ok, False otherwise
	 */
	protected function appendAudio($list) {
    // HTTP header options
    $opts = array(
      'http'=>array(
      	'method'=>"GET",
      	'header'=>
  				"Referer: \r\n" .
          "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36\r\n"
      )
    );
    $http_context = stream_context_create($opts);
    $http_url = 'http://translate.google.com/translate_tts?ie=UTF-8';
    // add list elements
    foreach ($list as $num=>$element) {
      // tts api
      $url = $http_url . '&tl=' . $this->language . '&q=' . urlencode($element) . '&total=' . count($list) . '&idx=' . $num;
      // get audio file
      $audio = file_get_contents($url, false, $http_context);
      if (!$audio) {
        // fatal error
        $this->log('Unable to create audio by Google TTS API "'.$url.'"', __METHOD__, TL_ERROR);
        return false;
      }
      // save audio part
      fwrite($this->mp3_file, $audio);
    }
    return true;
  }

	/**
	 * Append a pause to a MP3 file
	 *
	 * @param int $ms  Time length in ms
	 *
	 * @return bool  True if everithing is ok, False otherwise
	 */
	protected function appendPause($ms) {
    for ($cnt = round($ms / 100); $cnt > 0; $cnt--) {
      // save audio pause
      fwrite($this->mp3_file, $this->mp3_pause);
    }
    return true;
  }

}

