### Hubzilla-Entwicklung - ein Leitfaden für das Schemasystem 

Ein Schema ist, kurz gesagt, eine Sammlung von Einstellungen für eine Reihe von Variablen, die bestimmte Elemente eines Themes definieren. Ein Schema wird so geladen, als wäre es Teil der config.php und hat Zugang zu allen Informationen. Das bedeutet, dass es identitätsbewusst ist und für einige interessante Dinge verwendet werden kann. Man könnte z. B. die Optionen nach Dienstklassen einschränken oder verschiedenen Mitgliedern unterschiedliche Optionen anbieten.

Standardmäßig filtern wir nur danach, ob der Expertenmodus aktiviert ist oder nicht. Wenn der Expertenmodus aktiviert ist, werden dem Mitglied alle Optionen angezeigt. Ist dies nicht der Fall, stehen nur Schema, Hintergrundbild, Schriftart und Iconset zur Auswahl.

Ein Schema wird *erst nach* den persönlichen Einstellungen des Mitglieds geladen. Um einem Mitglied die Möglichkeit zu geben, einen bestimmten Aspekt eines Schemas zu überschreiben, würden Sie daher die folgende Syntax verwenden:

```
    if (! $foo)
        $foo = 'bar';
```

Es gibt jedoch Umstände - insbesondere bei positionalen Elementen -, unter denen es wünschenswert (oder notwendig) sein kann, die Einstellungen eines Mitglieds zu überschreiben. In diesem Fall ist die Syntax noch einfacher:

```
        $foo = 'bar';
```

Die Mitglieder werden Ihnen dies jedoch nicht danken, also verwenden Sie es nur, wenn es erforderlich ist.
Wenn keine persönlichen Optionen eingestellt sind und kein Schema ausgewählt ist, wird zunächst versucht, ein Schema mit dem Dateinamen „default.php“ zu laden. Diese Datei sollte niemals in ein Thema eingebunden werden. Wenn dies der Fall ist, wird es zu Konflikten kommen, wenn andere ihren Code aktualisieren. Vielmehr sollte dies von den Administratoren für jede Seite einzeln festgelegt werden. default.php und default.css MÜSSEN Symlinks zu bestehenden Schemadateien sein.

Ihr Schema muss - und sollte - nicht alle diese Werte enthalten. Nur die Werte, die von den Standardwerten abweichen, sollten aufgeführt werden. Dies gibt Ihnen einige sehr mächtige Optionen mit sehr wenigen Zeilen Code.

Beachten Sie, dass die verfügbaren Optionen von Thema zu Thema unterschiedlich sind. Für das Redbasic-Theme sind folgende Optionen verfügbar:

- nav_colour Die Farbe der Navigationsleiste. Die Optionen sind rot, schwarz und silber. Alternativ kann man $nav_bg_1, $nav_bg_2, $nav_bg_3 und $nav_bg_4 einstellen, um Farbverläufe und Hover-Effekte zu erzielen.
- banner_colour Die Schriftfarbe des Bannerelements. Akzeptiert einen RGB- oder Hex-Wert.
- bgcolour Legt die Hintergrundfarbe des Körpers fest. Akzeptiert einen RGB- oder Hex-Wert.
- background_image Legt ein Hintergrundbild fest. Akzeptiert eine URL oder einen Pfad.
- item_colour Legt die Hintergrundfarbe von Elementen fest. Akzeptiert einen RGB- oder Hex-Wert.
- item_opacity Legt die Deckkraft der Elemente fest. Akzeptiert einen Wert von 0,01 bis 1
- toolicon_colour Legt die Farbe der Werkzeugsymbole fest. Akzeptiert einen RGB- oder Hex-Wert.
- toolicon_activecolour Legt die Farbe der aktiven oder mit dem Mauszeiger gehaltenen Werkzeugsymbole fest.
- font_size Legt die Größe der Schriftarten in Artikeln und Beiträgen fest. Akzeptiert px oder em.
- body_font_size Legt die Größe der Schriftarten auf der Body-Ebene fest. Akzeptiert px oder em.
- font_colour Legt die Farbe der Schrift fest. Akzeptiert einen RGB- oder Hex-Wert.
- radius Legt den Radius der Ecken fest. Akzeptiert einen Zahlenwert und ist immer in px.
- shadow Legt die Größe der Schatten fest, die bei Inline-Bildern angezeigt werden. Akzeptiert einen numerischen Wert. Hinweis: Schatten werden nicht auf Smileys angewendet.
- converse_width Legt die maximale Breite des Inhaltsbereichs in px fest.
- nav_min_opacity
- top_photo
- antwort_foto

Wenn eine Datei your_schema_name.css gefunden wird, wird der Inhalt dieser Datei an das Ende von style.css angehängt. Dies gibt dem Schema-Entwickler die Möglichkeit, jede Style-Komponente zu überschreiben.