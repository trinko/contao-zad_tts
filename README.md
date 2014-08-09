ZAD Text-To-Speech - An extension for Contao CMS
------------------------------------------------
This extension adds to news a text-to-speech functionality.
It uses the "unofficial" Google TTS API.


How it works
------------
Use the Contao Repository to install this extension in your Contao website.

Once installed, go to News Archive management.
Here you can enable TTS and set the folder where to save MP3 audio files.

Once enabled a news archive, you have to manually create the audio file for each news.
To do this task, go to News management and use the new "Update TTS" button for each news you want.
Note that if you change anything in a news, then you have to recreate manually the audio file.

To show the TTS News in the frontend, use the module "TTS News Reader" instead of "Newsreader" one.
The module can be configured as follows:
* TTS News Archives: choose one or more news archives with TTS enabled;
* TTS Player: enable the TTS Player;
* TTS Download: enable the TTS audio file download.

*Note that if you want to show the TTS Player, then you have to include the jQuery library in your page layout.*

That's all, folks!
