### Eingebaute automatische Verschlüsselung 

Vollständige Offenlegung: Die Verschlüsselung, die hubzilla standardmäßig verwendet, ist nicht absolut wasserdicht. Es *gibt* bekannte Verfahren, um sie zu umgehen. Das ist*aber* sehr aufwändig und muss für jeden Kanal einzeln durchgeführt werden. Und um das klarzustellen: Andere Dienste speichern Ihre Nachrichten im Klartext, daher sehen wir diesen Ansatz als eine *deutliche* Verbesserung für Ihre Privatsphäre an. Außerdem steht es Ihnen jederzeit frei, weitere Verschlüsselungen und Passwortschutz zu verwenden, wenn Sie dies wünschen.
Um dies näher zu erläutern: 

- jeder Kanal hat sein Schlüsselpaar
- jeder nicht-öffentliche Beitrag wird automatisch verschlüsselt
- optionaler Passwortschutz für Inhalte über Krypto-Javascript, Browser-zu-Browser-Verschlüsselung (muss in den Einstellungen aktiviert werden) Vollständige Offenlegung: Ein böswilliger Hub-Administrator könnte bösartigen Javascript-Code (z.B. Keylogging-Fähigkeiten) in den Code einschleusen. Verschlüsseln Sie unsere Daten mit GPG, werden Sie selbst Hub-Administrator oder verwenden Sie andere Kommunikationsmittel, wenn Sie das stört.

Was ist also der Umfang der Sicherheit? Um es ganz offen zu sagen: Das mag toll sein, aber es ist nicht perfekt.

- Jeder nicht öffentliche Beitrag wird automatisch verschlüsselt, aber Personen, die Zugriff auf die Datenbank und die Dateien der Website haben *, können* mit Hilfe dieser Schlüssel, die natürlich auf dem Server gespeichert werden müssen, alles entschlüsseln. Um es klar zu sagen: Die Verschlüsselungsschlüssel sind für jeden Kanal unterschiedlich und es ist ein *ziemlicher Aufwand*, dies zu tun. Und nochmals: Andere Dienste speichern Ihre Nachrichten im Klartext und unverschlüsselt. Das ist also *ein* ganz erheblicher Gewinn für Ihre Privatsphäre.

Wir glauben, dass eine Massenüberwachung auf NSA-Niveau mit Klartextextraktion aufgrund des Designs des Nomad-Protokolls wahrscheinlich nicht möglich ist. Gezielte Angriffe, wie z. B. das Hacken eines Hubs, um an die Serverprotokolle und die Datenbank zu gelangen, geben nur teilweise Aufschluss darüber, was zwischen der Kommunikation von Personen zwischen verschiedenen Hubs vor sich geht. Wir glauben, dass dies es für Angreifer auf staatlicher Ebene viel teurer macht, auf Ihre Inhalte in hubzilla zuzugreifen.
Wir nehmen gerne Hilfe an, um die Sicherheit des Systems zu verbessern und es auch zu überprüfen.