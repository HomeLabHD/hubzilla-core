### Klonen

Hubzilla-Kanäle verfügen über eine sogenannten "nomadische Identität". Das ist eine Spezialität des Nomad-Protokolls, auf welchem Hubzilla basiert und mit welchem Hubzilla-Hubs untereinander kommunizieren.

Durch die nomadische Identität ist es möglich, Klone des eigenen Kanals zu erzeugen, was die Zensur- und Ausfallresistenz enorm erhöht.

Wenn Sie Ihren Kanal geklont haben, ist es kein Problem, wenn Ihr "Heimat-Hub" einmal ausfällt oder nicht korrekt funktioniert. Sie können übergangslos mit einem Kanal-Klon, welcher auf einem anderen Hub liegt, weiter am Fediverse teilnehmen.

Sämtliche Kanal-Klone werden automatisch im Hintergrund synchronisiert.

Um einen Klon Ihres Kanals anzulegen, benötigen sie einen Account auf einem weiteren (anderen) Hubzilla-Hub.

Es gibt nun mehrere Möglichkeiten, auf diesem anderen Hub, einen Klon Ihres Kanals anzulegen.

Sie können mit der App "Kanalexport" den Kanal auf Ihrem "Heimat-Hub" exportieren.

![Klone 01](/help/de/member/pic/klone01.png)

Durch einen Klick auf den Button "Kanal exportieren", exportieren Sie Ihre Identität und Ihren sozialen Graph in eine Datei, welche sie herunterladen können.

Da Dateien, Webseiten, Wikis, Kalender und Chaträume immer lokal auf den eigenen Hub (also den jeweiligen Server) beschränkt sind, können sie mit der App "Kanalexport" auch Archive dieser Daten lokal herunterladen.

Loggen Sie sich nun auf dem neuen Hub ein und wählen Sie entweder im Hauptmenü (Ihr Profilbild; links oben) den Menüpunkt "Kanäle" und auf der erscheinenden Kanalauswahl-Seite den Button "+ Neu anlegen" oder rufen Sie direkt die Kanalerstellungs-Seite unter `<URL-Ihres-Hubs>/new_channel` auf.

![Klone 02](/help/de/member/pic/klone02.png)

![Klone 03](/help/de/member/pic/klone03.png)

Auf dieser Seite geben Sie nun aber keine Informationen für das Erstellen eines neuen Kanals ein, sondern wählen am unteren Ende des Dialogs den Link "importiere einen bestehenden Kanal von einem anderen Server".

![Klone 04](/help/de/member/pic/klone04.png)

Nun öffnet sich der Dialog für den Kanal-Import.

![Klone 05](/help/de/member/pic/klone05.png)

Mit dem Button "Hochzuladende Datei: Durchsuchen..." öffnen Sie einen Dateidialog, mit welchem Sie die zuvor gespeicherte Kanal-Datei auswählen können.

Alternativ zu dieser Methode können Sie Ihren Kanal auch direkt von der Quelle, also dem Ursprungs-Hub klonen. Dafür müssen Sie im Kanal-Import-Dialog das Handle des zu klonenden Kanals, die Mailadresse für den Login beim Ursprungs-Hub, sowie das dazugehörige Passwort angeben. Zusätzlich können sie per Schalter auswählen, ob Dateien und Objekte des Ursprungs-Hubs mit importiert werden sollen (sofern Ihr neuer Hub das zulässt und das Speicherlimit ausreicht).

Im Dialog können Sie außerdem noch festlegen, ob der neue Hub Ihr "primärer Hub sein soll". Das bedeutet, dass der neue Kanal (also der nun anzulegende) Ihr primärer Kanal wird. Das hat Auswirkungen auf das Handle Ihres Kanals, welches damit künftig mit der URL des neuen Hubs endet.

In der Regel, sofern man nicht komplett auf einen anderen Hub umziehen möchte, lässt man den Schalter bei "Nein" und der Primäre Hub bleibt derjenige, der es aktuell ist (das Handle bleibt unverändert).

Sollten Sie eine veränderte Kanalbezeichnung (Kurzname) wünschen, können Sie diese in einem weiteren Eingabefeld eingeben. Lassen Sie das Feld leer, bleibt der Kanal-Kurzname unverändert.

*<u>Hinweis:</u> Sollte der Kanal-Kurzname auf dem neuen Hub bereits vergeben sein (oder gesperrt, weil ein gleichlautender Kanal dort bereits existierte, aber wieder gelöscht wurde), wird der Kurzname vom System automatisch modifiziert.*

Schließlich klicken Sie auf "Absenden" und verlassen Sie die Seite NICHT, bis der Import abgeschlossen ist. Dies kann, je nach Umfang des Ursprungskanals einige Zeit in Anspruch nehmen.

Über Einstellungen → Klon-Adressen verwalten können Sie jederzeit Ihre Klone verwalten. Sie können festlegen, welches der "primäre Hub" ist und Sie können Klone löschen, wobei empfohlen wird, geklonte Kanäle besser direkt auf dem jeweiligen Hub zu löschen.

![Klone 06](/help/de/member/pic/klone06.png)
