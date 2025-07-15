### Translations 

Our translations are managed through Transifex. If you would like to help translate Hubzilla into another language, sign up at transifex.com, visit [Transifex](https://www.transifex.com/Friendica/hubzilla/?f=&zid=pepecyb@hub.hubzilla.hu) and apply to join one of the existing language teams or create a new one. Notify one of the lead developers if you have a translation update that needs merging, or ask if you can merge it yourself if you are familiar with Git and PHP. We have a string file called ‘messages.po’ which is gettext compatible, and a handful of email templates from which we automatically generate the application language files. 

#### The translation process   

The strings used in Hubzilla's user interface are translated at [Transifex](https://www.transifex.com/Friendica/hubzilla/?f=&zid=pepecyb@hub.hubzilla.hu) and then added to the Git repository on github. If you would like to help translate a language, be it correcting terms or translating Hubzilla into a currently unsupported language, please register an account on transifex.com and contact the translation team there.

Translating Hubzilla is easy. Simply use the online tool on transifex. If you don't want to bother with Git & Co. it's no problem, we regularly check the status of the translations and import them into the source tree on github for others to use.

We do not include every translation of transifex in the source tree to avoid a scattered and disrupted overall experience. As a rough estimate, we have a lower limit of 50% translated strings before we include the language. This limit is only based on the amount of translated strings, assuming that the most important strings for the user interface are translated first by a translation team. If you feel that your translation is usable before this limit, please contact us and we will likely include your team's work in the source tree.
If you would like to add your work to the source tree yourself, please feel free to do so and contact us with any questions that arise. The process is simple and Hubzilla comes with all the necessary tools.

The location of the translated files in the source tree is

`/View/LNG-CODE/`

where LNG-CODE is the code of the language used, e.g. de for German or fr for French. For the e-mail templates (the *.tpl files), simply place them in the directory and you're done. The translated strings come as an ‘hmessages.po’ file from Transifex, which needs to be translated into the PHP file that Hubzilla uses. To do this, place the file in the directory mentioned above and use the ‘po2php’ utility from the util directory of your Hubzilla installation.

Assuming you want to convert the German localisation, which is located in view/en/hmessages.po, you would proceed as follows.

1. At the command prompt, navigate to the base directory of your

Hubzilla installation

1. Execute the po2php script that translates the

into the hstrings.php file used by Hubzilla.

`$> php util/po2php.php view/en/hmessages.po`

The output of the script will be placed in the view/en/hstrings.php file where froemdoca expects it, so you can test your translation immediately.

1. Visit your Hubzilla page and check that it is in the language you just translated.

language you have just translated. If not, try to find the error, probably PHP will give you a hint in the log/warnings.about the error.

For debugging you can also try to ‘execute’ the file with PHP. This should give no output if the file is ok, but may give you a hint for the error in the file.

`$> php view/en/hstrings.php`

1. Commit the two files to your git repository with a meaningful commit message, push it to your fork of the Hubzilla repository on github and make a pull request for this commit.

#### Utilities   

In addition to the po2php script, there are several other utilities for translation in the ‘util’ directory of the Hubzilla source tree. If you just want to translate Hubzilla into another language, you probably won't need any of these tools, but it will give you an idea of how Hubzilla's translation process works.
See the utils/README file for more information.

#### Known issues   

\* Hubzilla uses the language setting of the visitor's browser to set the language for the user interface. Most of the time this works, but there are some known quirks. * the early translations are based on the friendica translations, if you find some rough translations please let us know or correct them at Transifex.