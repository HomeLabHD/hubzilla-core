### Erstellen von Plugins/Addons für Hubzilla 

Sie möchten also, dass Hubzilla etwas tut, was es noch nicht tut. Da gibt es viele Möglichkeiten. Aber lass uns lernen, wie man ein Plugin oder Addon schreibt. 
In deinem Hubzilla-Ordner/Verzeichnis wirst du wahrscheinlich ein Unterverzeichnis namens 'addon' finden. Wenn Sie noch keins haben, erstellen Sie es einfach. 

```
mkdir addon
```

Überlegen Sie sich dann einen Namen für Ihr Addon. Wahrscheinlich haben Sie zumindest eine vage Vorstellung davon, was es tun soll. Für unser Beispiel werde ich ein Plugin mit dem Namen „randplace“ erstellen, das einen zufälligen Speicherort für jeden Ihrer Beiträge angibt. Der Name Ihres Plugins wird verwendet, um die Funktionen zu finden, auf die wir zugreifen müssen, und ist Teil des Funktionsnamens, also verwenden Sie zur Sicherheit nur einfache Textzeichen.
Sobald Sie einen Namen gewählt haben, erstellen Sie ein Verzeichnis unterhalb von 'addon', um Ihre Arbeitsdatei(en) zu speichern.

```
mkdir addon/randplace
```

Erstellen Sie nun Ihre Plugin-Datei. Sie muss den gleichen Namen haben und ist ein PHP-Skript. Erstellen Sie also mit Ihrem Lieblingseditor die Datei

```
addon/randplace/randplace.php
```

Die allererste Zeile dieser Datei muss lauten

```
<?php
```

Dann werden wir einen Kommentarblock erstellen, der das Plugin beschreibt. Hierfür gibt es ein spezielles Format. Wir verwenden /* ... */ Kommentar-Stil und einige markierte Zeilen, die aus



	/**
	 *
	 * Name: Random Place (here you can use better descriptions than you could in the filename)
	 * Description: Sample Hubzilla plugin, Sets a random place when posting.
	 * Version: 1.0
	 * Author: Mike Macgirvin <mike@zothub.com>
	 *
	 */

Diese Tags sind für den Website-Administrator sichtbar, wenn er Plugins über die Verwaltungskonsole installiert oder verwaltet. Es kann mehr als einen Autor geben. Fügen Sie einfach eine weitere Zeile hinzu, die mit „Autor:“ beginnt.
Ein typisches Plugin hat mindestens die folgenden Funktionen:

- pluginname_load()
- pluginname_unload()

In unserem Fall werden wir sie randplace_load() und randplace_unload() nennen, da dies der Name unseres Plugins ist. Diese Funktionen werden immer dann aufgerufen, wenn wir das Plugin entweder initialisieren oder von der aktuellen Webseite entfernen wollen. Auch wenn Ihr Plugin Dinge wie die Änderung des Datenbankschemas erfordert, bevor es zum ersten Mal ausgeführt werden kann, würden Sie diese Anweisungen wahrscheinlich in den Funktionen namens

- pluginname_install()
- pluginname_uninstall()

Als nächstes werden wir über **Hooks** sprechen. Hooks sind Stellen im Hubzilla-Code, an denen wir Plugins erlauben, etwas zu tun. Es gibt eine [Menge davon](help/hooks), und jedes hat einen Namen. Normalerweise verwenden wir die Funktion pluginname_load(), um eine „Handler-Funktion“ für alle Hooks zu registrieren, an denen Sie interessiert sind. Wenn dann einer dieser Hooks ausgelöst wird, wird Ihr Code aufgerufen.
Wir registrieren Hook-Handler mit der Funktion 'register_hook()'. Sie benötigt 3 Argumente. Das erste ist der Hook, den wir abfangen wollen, das zweite ist der Dateiname der Datei, in der sich unsere Handler-Funktion befindet (relativ zur Basis Ihrer Hubzilla-Installation), und das dritte ist der Funktionsname Ihrer Handler-Funktion. Lassen Sie uns also jetzt unsere randplace_load()-Funktion erstellen. 


	function randplace_load() {
	    register_hook('post_local', 'addon/randplace/randplace.php', 'randplace_post_hook');
	
		register_hook('feature_settings', 'addon/randplace/randplace.php', 'randplace_settings');
		register_hook('feature_settings_post', 'addon/randplace/randplace.php', 'randplace_settings_post');
	
	}

Wir werden also drei Ereignisse abfangen: „post_local“, das ausgelöst wird, wenn ein Beitrag im lokalen System erstellt wird, „feature_settings“, um einige Einstellungen für unser Plugin festzulegen, und „feature_settings_post“, um diese Einstellungen zu speichern.

Als nächstes erstellen wir eine unload-Funktion. Das ist ganz einfach, denn sie hebt nur die Registrierung unserer Hooks auf. Sie nimmt genau die gleichen Argumente entgegen.

	function randplace_unload() {
	    unregister_hook('post_local', 'addon/randplace/randplace.php', 'randplace_post_hook');
	
		unregister_hook('feature_settings', 'addon/randplace/randplace.php', 'randplace_settings');
		unregister_hook('feature_settings_post', 'addon/randplace/randplace.php', 'randplace_settings_post');
	
	}

Hooks werden mit zwei Argumenten aufgerufen. Das erste ist immer $a, das unsere globale App-Struktur ist und eine riesige Menge an Informationen über den Zustand der Web-Anfrage enthält, die wir gerade verarbeiten, sowie darüber, wer der Betrachter ist, und was unser Login-Status ist, und der aktuelle Inhalt der Webseite, die wir wahrscheinlich aufbauen.

Das zweite Argument ist spezifisch für den Hook, den Sie aufrufen. Es enthält Informationen, die für diese bestimmte Stelle im Programm relevant sind, und ermöglicht es Ihnen oft, sie anzusehen und sogar zu ändern. Um sie zu ändern, müssen Sie dem Variablennamen ein '&' hinzufügen, damit sie als Referenz an Ihre Funktion übergeben wird. Andernfalls wird eine Kopie erstellt, und alle Änderungen, die Sie vornehmen, gehen verloren, wenn der Hook-Prozess zurückkehrt. Normalerweise (aber nicht immer) ist das zweite Argument ein benanntes Array von Datenstrukturen. Bitte lesen Sie die „Hook-Referenz“ (zu diesem Zeitpunkt noch nicht geschrieben) für Details zu jedem spezifischen Hook. Gelegentlich müssen Sie sich den Programmcode ansehen, um genau zu sehen, wie ein bestimmter Hook aufgerufen wird und wie die Ergebnisse verarbeitet werden. 

Fügen wir nun etwas Code hinzu, um unseren post_local-Hook-Handler zu implementieren.

	function randplace_post_hook($a, &$item) {
	
	    /**
		 *
	     * An item was posted on the local system.
		 * We are going to look for specific items:
	     *      - A status post by a profile owner
		 *      - The profile owner must have allowed our plugin
	     *
		 */
	
	    logger('randplace invoked');
	
	    if(! local_channel())   /* non-zero if this is a logged in user of this system */
	        return;
	
	    if(local_channel() != $item['uid'])    /* Does this person own the post? */
	        return;
	
	    if(($item['parent']) || (! is_item_normal($item))) {
		    /* If the item has a parent, or isn't "normal", this is a comment or something else, not a status post. */
	        return;
		}
	
	    /* Retrieve our personal config setting */
	
	    $active = get_pconfig(local_channel(), 'randplace', 'enable');
	
		if(! $active)
	    	return;
	    /**
		 *
	     * OK, we're allowed to do our stuff.
		 * Here's what we are going to do:
	     * load the list of timezone names, and use that to generate a list of world cities.
		 * Then we'll pick one of those at random and put it in the "location" field for the post.
	     *
		 */
	
	    $cities = array();
		$zones = timezone_identifiers_list();
	    foreach($zones as $zone) {
		    if((strpos($zone,'/')) && (! stristr($zone,'US/')) && (! stristr($zone,'Etc/')))
	    	    $cities[] = str_replace('_', ' ',substr($zone,strpos($zone,'/') + 1));
	    }
	
		if(! count($cities))
	    	return;
	    $city = array_rand($cities,1);
		$item['location'] = $cities[$city];
	
	    return;
	}


Fügen wir nun unsere Funktionen zum Erstellen und Speichern von Einstellungswerten hinzu.

	/**
	 *
	 * Callback from the settings post function.
	 * $post contains the global $_POST array.
	 * We will make sure we've got a valid user account 
	 * and that only our own submit button was clicked
	 * and if so set our configuration setting for this person.
	 *
	 */
	
	function randplace_settings_post($a,$post) {
	    if(! local_channel())
	        return;
	    if($_POST['randplace-submit'])
	        set_pconfig(local_channel(),'randplace','enable',intval($_POST['randplace']));
	}
	
	/**
	 *
	 * Called from the Feature Setting form.
	 * The second argument is a string in this case, the HTML content region of the page.
	 * Add our own settings info to the string.
	 *
	 * For uniformity of settings pages, we use the following convention
	 *     <div class="settings-block">
	 *       <h3>title</h3>
	 *       .... settings html - many elements will be floated...
	 *       <div class="clear"></div> <!-- generic class which clears all floats -->
	 *       <input type="submit" name="pluginnname-submit" class="settings-submit" ..... />
	 *     </div>
	 */
	
	function randplace_settings(&$a,&$s) {
	
	    if(! local_channel())
	        return;
	
	    /* Add our stylesheet to the page so we can make our settings look nice */
	
	    head_add_css('/addon/randplace/randplace.css');
	
	    /* Get the current state of our config variable */
	
	    $enabled = get_pconfig(local_channel(),'randplace','enable');
	
	    $checked = (($enabled) ? ' checked="checked" ' : '');
	
	    /* Add some HTML to the existing form */
	
	    $s .= '<div class="settings-block">';
	    $s .= '<h3>' . t('Randplace Settings') . '</h3>';
	    $s .= '<div id="randplace-enable-wrapper">';
	    $s .= '<label id="randplace-enable-label" for="randplace-checkbox">' . t('Enable Randplace Plugin') . '</label>';
	    $s .= '<input id="randplace-checkbox" type="checkbox" name="randplace" value="1" ' . $checked . '/>';
	    $s .= '</div><div class="clear"></div>';
	
	    /* provide a submit button */
	
	    $s .= '<div class="settings-submit-wrapper" ><input type="submit" name="randplace-submit" class="settings-submit" value="' . t('Submit') . '" /></div></div>';
	
	}

#### *Erweiterte Plugins* 

Manchmal möchten Ihre Plugins eine Reihe neuer Funktionen bereitstellen, die gar nicht oder nur umständlich über Hooks bereitgestellt werden können. In diesem Fall kann Ihr Plugin auch als „Modul“ fungieren. Ein Modul ist in unserem Fall ein strukturierter Webpage-Handler, der auf eine bestimmte URL reagiert. Dann wird alles, was auf diese URL zugreift, vollständig von Ihrem Plugin behandelt.
Der Schlüssel dazu ist die Erstellung einer einfachen Funktion namens pluginname_module(), die nichts tut. 

```
function randplace_module() { return; }
```

Sobald diese Funktion existiert, wird die URL `https://yoursite/randplace` auf Ihr Plugin als Modul zugreifen. Dann können Sie Funktionen definieren, die an verschiedenen Stellen aufgerufen werden, um eine Webseite aufzubauen, genau wie die Module im mod/-Verzeichnis. Die typischen Funktionen und die Reihenfolge, in der sie aufgerufen werden, sind

	modulename_init($a)    // (e.g. randplace_init($a);) called first - if you wish to emit json or xml, 
	                       // you should do it here, followed by killme() which will avoid the default action of building a webpage
	modulename_aside($a)   // Often used to create sidebar content
	modulename_post($a)    // Called whenever the page is accessed via the "post" method
	modulename_content($a) // called to generate the central page content. This function should return a string 
	                       // consisting of the central page content.

Ihre Modulfunktionen haben Zugriff auf den URL-Pfad, als ob sie eigenständige Programme im Unix-Betriebssystem wären. Wenn Sie zum Beispiel die Seite 	

```
https://yoursite/randplace/something/somewhere/whatever
```

besuchen, erstellen wir eine argc/argv-Liste zur Verwendung durch Ihre Modulfunktionen

	$x = argc(); $x will be 4, the number of path arguments after the sitename
	
	for($x = 0; $x < argc(); $x ++)
		echo $x . ' ' . argv($x);


	0 randplace
	1 something
	2 somewhere
	3 whatever

#### *Portierung von Friendica-Plugins* 

Hubzilla verwendet eine ähnliche Plugin-Architektur wie das Friendica-Projekt. Die Authentifizierungs-, Identitäts- und Rechtesysteme sind jedoch völlig unterschiedlich. Viele Friendica-Plugins können relativ einfach portiert werden, indem man ein paar Funktionen umbenennt - und dann sicherstellt, dass das Berechtigungsmodell befolgt wird. Die Funktionen, die umbenannt werden müssen, sind:

- Friendica's pluginname_install() wird zu pluginname_load()
- Friendicas pluginname_uninstall() wird zu pluginname_unload()

Hubzilla hat _install und _uninstall Funktionen, aber diese werden unterschiedlich benutzt.

- Friendicas „plugin_settings“-Haken wird „feature_settings“ genannt
- Friendicas „plugin_settings_post“-Haken wird „feature_settings_post“ genannt

Wenn Sie diese ändern, wird Ihr Plugin oft funktionieren, aber bitte überprüfen Sie Ihren gesamten Berechtigungs- und Identitätscode, da die Konzepte dahinter in Hubzilla völlig anders sind. Viele strukturierte Datennamen (insbesondere DB-Schema-Spalten) sind auch ganz anders.