### Bookmarks (Lesezeichen))

Lesezeichen geben einen Link an, der in Ihrem Lesezeichenordner  gespeichert werden kann. Sie verwenden die Zeichenfolge `#^` gefolgt von  dem Link. Oft werden diese automatisch generiert. Wenn der Administrator des Hub das Addon  "bookmarker" installiert hat, wird diese Sequenz beim Online-Ansehen des Beitrags oder Kommentars in ein Lesezeichensymbol konvertiert.

![Bookmarks01](/help/de/member/pic/bookm01.png)

![Bookmarks02](/help/de/member/pic/bookm02.png)

Wenn Sie auf das Symbol klicken, wird das Lesezeichen gespeichert. Wenn das Add-In für das Lesezeichen nicht  installiert ist, enthält das Post-Dropdown-Menü einen Link zum Speichern des Lesezeichens oder der Lesezeichen.

Um Lesezeichen zu verwenden, müssen Sie die App "Lesezeichen" installieren.

Die App führt dann alle von Ihnen gesetzten Lesezeichen auf.

Um ein Lesezeichen unabhängig von einem Link im Stream oder einem Beitrag hinzuzufügen, können Sie die Seite `<URL_Ihres_hub>/rbmark` aufrufen, welche Ihnen eine Maske zur manuellen Eingabe eines Lesezeichens zur Verfügung stellt.

![Bookmarks03](/help/de/member/pic/bookm03.png)

Sie können auch ein [Bookmarklet](https://de.wikipedia.org/wiki/Bookmarklet) erstellen und z.B. in der Lesezeichenleiste Ihres Webbrowsers ablegen:

```javascript
javascript:javascript:(function(){var%20url=location.href;var%20title=document.title||url;window.open('[observer.baseurl]/rbmark?&url='+encodeURIComponent(url)+'&title='+encodeURIComponent(title)+'&source=bookmarklet','_blank','menubar=no,height=390,width=600,toolbar=no,scrollbars=no,status=no,dialog=1');})();
```

<u>Wichtig:</u> Ersetzen Sie den Ausdruck `[observer.baseurl]` durch die URL Ihres Hub, also wenn Sie z.B. Ihren Kanal bei Klackerhub haben, müssen Sie für `[observer.baseurl]` einfach `https://klacker.org` eintragen.

Nun können Sie jede Webseite, welche Sie aufsuchen mit einem Klick auf das Bookmarklet in die Lesezeichen Ihres Kanals übernehmen.
