### Administration

#### Website-Verwaltung 

Die Verwaltung der Website erfolgt in der Regel über die Verwaltungsseite, die sich unter /admin auf Ihrer Website befindet. Um auf diese Seite zugreifen zu können, müssen Sie über Verwaltungsrechte für den Server verfügen. Administrationsrechte werden dem ersten Konto gewährt, das sich auf Ihrer Website registriert, **vorausgesetzt,** die E-Mail-Adresse dieses Kontos stimmt genau mit der E-Mail-Adresse überein, die Sie bei der Einrichtung als E-Mail-Adresse des Administrators angegeben haben.

![adm01](/help/de/pic/adm01.png)

Es gibt mehrere Möglichkeiten, wie dies fehlschlagen und das System ohne ein Administratorkonto bleiben kann, z. B. wenn das erste Konto, das erstellt wurde, eine andere E-Mail-Adresse als die bei der Einrichtung angegebene Administrator-E-Mail-Adresse angegeben hat.

Aus Sicherheitsgründen gibt es auf dem System keine Webseite oder Schnittstelle, die Ihnen Administratorrechte verleiht. Wenn Sie eine Situation korrigieren müssen, in der ein System kein Administratorkonto hat, **muss** dies durch Bearbeiten der Kontotabelle in der Datenbank geschehen. Es gibt keine andere Möglichkeit. Dazu müssen Sie den Eintrag in der Kontotabelle finden, der zu dem gewünschten Administrator gehört, und „account_roles“ für diesen Eintrag auf 4096 setzen. Anschließend können Sie die Administratorseite über das Profilmenü Ihres Systems oder direkt über /admin aufrufen.

Ein Hub kann mehrere Administratoren haben und die Anzahl der Administratoren ist nicht begrenzt. Wiederholen Sie den obigen Vorgang für jedes Konto, das Sie mit Administrationsrechten ausstatten möchten.