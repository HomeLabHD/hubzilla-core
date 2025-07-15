### Verbinden mit Kanälen  

Verbindungen in Hubzilla können sehr viele verschiedene Bedeutungen haben. Eine Verbindung ist genauer definiert als eine Reihe von Berechtigungen, die Sie einer anderen Person erteilt haben. In herkömmlichen sozialen Netzwerken erhalten alle Verbindungen die gleichen Berechtigungen oder höchstens zwei Stufen (Freunde und „Follower“). In Hubzilla kann eine Reihe von separaten Berechtigungen festgelegt/angepasst werden, abhängig von der Situation und der Beziehung, die Sie mit dem anderen Kanal haben. Sie können jemandem erlauben, Ihre Beiträge zu sehen, aber nicht Ihre Fotos. Sie können ihnen auch die Erlaubnis verweigern, Ihre Beiträge zu kommentieren oder private Nachrichten an Sie zu senden. Aber machen wir es uns einfach: Sie wollen mit jemandem befreundet sein, den Sie aus sozialen Netzwerken kennen. Wie machen Sie das?

Sie können sich das Verzeichnis ansehen. Das Verzeichnis ist auf allen Hubzilla-Websites verfügbar, d. h., wenn Sie von Ihrer eigenen Seite aus suchen, erhalten Sie Ergebnisse aus dem gesamten Netzwerk. Sie können nach Name, Interesse, Standort und Schlüsselwort suchen.

Wenn Sie das „Handle“ von jemandem bereits kennen, können Sie direkt mit ihm in Verbindung treten. Ein Handle sieht genauso aus wie eine E-Mail-Adresse (z. B. `bob@example.com`), verweist aber auf eine Person im offenen sozialen Netz. Um eine Verbindung herstellen zu können, muss ein kompatibles Netzwerkprotokoll verwendet werden. Standardmäßig unterstützt diese Software das Nomad-Protokoll, weitere Protokolle können jedoch über Plugins/Addons bereitgestellt werden. Weitere Informationen zur Verbindung mit Kanälen in anderen Netzwerken finden Sie weiter unten.

#### So verbinden Sie sich mit anderen Hubzilla-Kanälen:

Besuchen Sie das Profil des gewünschten Kanals, indem Sie auf ihr Foto im Verzeichnis, im Stream oder in den Kommentaren klicken, und es öffnet sich ihre Kanal-Homepage im Kanal-Betrachter. Auf der linken Seite des Bildschirms sehen Sie normalerweise einen Link mit der Bezeichnung „Verbinden“. Klicken Sie darauf, und schon sind Sie fertig. Je nach den Einstellungen des Kanals, mit dem Sie eine Verbindung herstellen möchten, müssen Sie möglicherweise warten, bis der Kanal Ihre Verbindung genehmigt hat, aber es sind keine weiteren Aktionen Ihrerseits erforderlich. Sobald Sie die Verbindung initiiert haben, werden Sie zum Verbindungseditor weitergeleitet. Hier können Sie bestimmte Berechtigungen für diesen Kanal zuweisen, wenn Sie Änderungen vornehmen möchten.

Sie können auch eine Verbindung zu einem beliebigen Kanal herstellen, indem Sie die Seite „Verbindungen“ Ihrer Website oder des Verzeichnisses aufrufen und den „Handle“ in das Feld „Neue Verbindung hinzufügen“ eingeben. Verwenden Sie diese Methode, wenn Ihnen jemand sein Handle mitteilt und Sie sich mit ihm verbinden möchten. Der Vorgang ist derselbe wie bei der Verbindung über die Schaltfläche „Verbinden“ - Sie werden dann zum Verbindungseditor weitergeleitet, um die Berechtigungen festzulegen.

#### So stellen Sie eine Verbindung zu Kanälen in anderen Netzwerken her:

Das Verfahren zum Verbinden mit "Kanälen" in anderen Netzwerken (wie z.B GNU-Social, Mastodon, Misskey, Pleroma und Diaspora) ist ähnlich - geben Sie deren „Handle“ in das Feld „+Add“ auf der Seite „Verbindungen“ ein. Bevor Sie dies jedoch tun, besuchen Sie bitte die App-Verwaltung im App-Menü  und vergewissern Sie sich, dass das entsprechende Protokoll (Diaspora, GNU-Social/OStatus oder ActivityPub) in Ihrem Hub bereitgestellt und **für Ihren Kanal** ***aktiviert*** ist. Diese Netzwerke/Protokolle unterstützen keine Kontomigration und Standortunabhängigkeit. Wenn Sie also den Standort wechseln oder Ihren Kanal anderswo klonen, kann die Kommunikation mit diesen Verbindungen fehlschlagen. Aus diesem Grund sind diese Protokolle nicht standardmäßig aktiviert, sondern nur mit Ihrer Zustimmung. Die Aktivierung dieser Protokolle ist eine wichtige Entscheidung zwischen der Kommunikation mit Freunden in diesen Netzwerken und der Ausfallsicherheit des Kontos, falls Ihr Server ausfällt.

Einige Kommunikationsnetze bieten mehr als ein Protokoll an. So können Sie sich beispielsweise mit jemandem verbinden, der sowohl das „ostatus“- alsauch das „activitypub“-Protokoll für die Kommunikation verwendet. Im Allgemeinen bietet das 'activitypub'-Protokoll eine bessere Erfahrung als das 'ostatus'-Protokoll, aber Hubzilla wählt oft das erste Protokoll, das es entdeckt, und das ist vielleicht nicht das, was Sie wollen. Sie können sich mit jemandem über ein bestimmtes Protokoll verbinden, indem Sie den Protokollnamen in eckigen Klammern vor dessen „Handle“ setzen. Zum Beispiel

`[activitypub]https://foo.bar/foobar`

`[ostatus]foobar@foo.bar`

`[diaspora]foobar@foo.bar`

`[zot]foobar@foo.bar`

`[feed]https://foo.bar/foobar`

#### So verbinden Sie sich mit RSS-Feeds:

Ihr Hub-Administrator kann die Verbindung mit RSS-Feeds erlauben. Das Verfahren für die Verbindung mit einem RSS-Feed ist dasselbe, außer dass Sie die URL des Feeds in das Feld „Neue Verbindung hinzufügen“ eingeben (oder einfügen). Die Möglichkeiten können durch den Administrator Ihres Hubs eingeschränkt sein, weil Verbindungen mit Feeds teilweise für hohe Systemlast sorgen.