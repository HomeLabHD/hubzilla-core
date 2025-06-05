### Das Nomad Protokoll

#### Was ist Nomad? 

Nomad ist das revolutionäre Protokoll, das Hubzilla antreibt und die **Kommunikation**, das **Identitätsmanagement** und die **Zugangskontrolle** in einem vollständig **dezentralisierten** Netzwerk unabhängiger Websites, oft als „das Grid“ bezeichnet, ermöglicht. Die daraus resultierende Plattform ist ein robustes System, das den Datenschutz und die Sicherheit unterstützt und gleichzeitig die Art von umfangreichen Webdiensten ermöglicht, die normalerweise nur in zentralisierten, proprietären Lösungen zu finden sind.

Betrachten Sie dieses typische Szenario: 

Jaquelina möchte Fotos von ihrem Blog unter **jaquelina.org** mit Roberto teilen, aber mit niemandem sonst. Roberto unterhält seinen eigenen Familien-Hub unter **roberto.net** auf einem völlig unabhängigen Server. Nomad erlaubt Jaquelina, ihre Fotos mit einer *Zugriffskontrollliste (ACL)* zu veröffentlichen, die nur Roberto einschließt. Das bedeutet, dass Roberto die Fotos zwar sehen kann, wenn er ihren Blog besucht, sein Bruder Marco jedoch nicht, und auch kein anderes Familienmitglied, das ein Konto auf **roberto.net** hat.

Der Clou an diesem Szenario ist die Tatsache, dass Roberto sich nie auf Jaquelinas Website angemeldet hat. Stattdessen musste er sich nur einmal mit seinem Passwort auf seiner *eigenen* Website **roberto.net** anmelden. Wenn Roberto **jaquelina.org** besucht, wird er von ihrem Hub nahtlos authentifiziert, indem sein Server im Hintergrund abgefragt wird. 

Es ist nicht ungewöhnlich, dass Server technische Probleme haben oder aus verschiedenen Gründen unzugänglich werden. Nomad bietet Robustheit für Robertos Online-Aktivitäten, indem es ihm erlaubt, *Klone* seiner Online-Identität oder seines *Kanals* auf mehreren unabhängigen Hubs zu haben. Stellen Sie sich vor, dass Robertos Server aus irgendeinem Grund zusammenbricht und er sich dort nicht mehr einloggen kann. Er meldet sich einfach bei einem seiner Klone auf **gadfly.com** an, einer Website, die von seinem Freund Peter betrieben wird. Sobald er sich bei **gadfly.com** authentifiziert hat, kann Roberto Jaquelinas Blog wie zuvor ansehen, ohne dass Jaquelina zusätzlichen Zugang gewähren muss!

#### Kommunikation 

Kommunikation und soziale Netzwerke sind ein wesentlicher Bestandteil des Grids. Jeder Kanal (und jeder Dienst, der von diesem Kanal bereitgestellt wird) kann die funktionsreiche soziale Kommunikation auf globaler Ebene in vollem Umfang nutzen. Diese Kommunikation kann öffentlich oder privat sein - und private Kommunikation umfasst nicht nur einen vollständig verschlüsselten Transport, sondern auch eine verschlüsselte Speicherung zum Schutz vor versehentlichem Schnüffeln und Offenlegung durch unseriöse Systemadministratoren und Internetdienstanbieter. 

Nomad unterstützt eine breite Palette von Hintergrunddiensten im Grid, von Freundschaftsvorschlägen bis hin zu Verzeichnisdiensten. Neue Inhalte und Datenaktualisierungen werden im Hintergrund zwischen den Hubs im gesamten Grid gemäß den Zugriffskontrolllisten und den von den *Sender- und* Empfängerkanälen festgelegten Berechtigungen weitergegeben. Die Daten werden auch zwischen einer beliebigen Anzahl von Kanalklonen synchronisiert, so dass Hub-Mitglieder auch dann auf Daten zugreifen und nahtlos weiter zusammenarbeiten können, wenn ihr primärer Hub nicht erreichbar oder offline ist.

#### Identität 

Die Identitätsschicht von Nomad ist einzigartig. Sie bietet ein **unsichtbares Single Sign-On** für alle Standorte im Grid. 

Sie bietet auch eine **nomadische Identität**, so dass Ihre Kommunikation mit Freunden, Familie oder anderen Personen, mit denen Sie kommunizieren, nicht durch den Verlust Ihres primären Kommunikationsknotens beeinträchtigt wird - weder vorübergehend noch dauerhaft. 

Die wichtigen Teile Ihrer Identität und Ihrer Beziehungen können auf einem USB-Stick oder Ihrem Laptop gesichert werden und jederzeit an einem beliebigen Knoten im Netz erscheinen - mit all Ihren Freunden und Vorlieben. 

Entscheidend ist, dass diese nomadischen Instanzen synchron gehalten werden, so dass jede Instanz übernehmen kann, wenn eine andere gefährdet oder beschädigt ist. Dies schützt Sie nicht nur vor größeren Systemausfällen, sondern auch vor vorübergehender Überlastung der Website und staatlicher Manipulation oder Zensur. 

Wir sind der Meinung, dass die nomadische Identität, die einmalige Anmeldung und die Dezentralisierung von Hubzilla ein hohes Maß an **Widerstandsfähigkeit** und **Beständigkeit** in die Internetkommunikation einbringen, die angesichts der weltweiten Tendenzen zur Zentralisierung von Unternehmen sowie der massenhaften und wahllosen staatlichen Überwachung und Zensur dringend benötigt werden.

Beim Durchsuchen des Netzes, beim Anzeigen von Kanälen und deren einzigartigen Inhalten, werden Sie nahtlos authentifiziert, sogar über völlig unterschiedliche Server-Hubs hinweg. Sie müssen keine Passwörter eingeben. Sie müssen nichts eintippen. Sie werden auf jeder neuen Seite, die Sie besuchen, einfach mit Ihrem Namen begrüßt. 

Wie funktioniert das bei Nomad? Wir nennen es **„magic-auth“**, weil Hubzilla die Details der Komplexität von Single-Sign-On-Logins und nomadischen Identitäten vor dem Surfen im Netz verbirgt.  Dies ist eines der Designziele von Hubzilla: die Privatsphäre und die Freiheit im Internet zu erhöhen und gleichzeitig die Komplexität und die Langeweile zu reduzieren, die durch die Notwendigkeit entstehen, für jeden Besuch im Internet neue Passwörter und Anmeldenamen einzugeben. Sie melden sich nur einmal auf Ihrem Home-Hub (oder einem von Ihnen gewählten nomadischen Backup-Hub) an. Dadurch können Sie auf alle authentifizierten Dienste zugreifen, die überall im Netz angeboten werden - wie Einkaufen, Blogs, Foren und Zugang zu privaten Informationen. Ihr Passwort wird nicht auf tausend verschiedenen Websites gespeichert, sondern auf Servern, die Sie kontrollieren oder denen Sie vertrauen.

Sie können nicht zum Schweigen gebracht werden. Sie können nicht aus dem Netz entfernt werden, es sei denn, Sie selbst entscheiden sich dafür, es zu verlassen.

#### Zugangskontrolle 

Die Identitätsschicht von Nomad ermöglicht es Ihnen, fein abgestufte Berechtigungen für jeden Inhalt zu vergeben, den Sie veröffentlichen möchten - und diese Berechtigungen erstrecken sich über das gesamte Grid. Das ist so, als hätte man eine riesige Website, die aus einer Armee kleiner individueller Websites besteht - und bei der jeder Kanal im Grid seine Datenschutz- und Freigabepräferenzen für alle von ihm erstellten Webressourcen vollständig kontrollieren kann. 

Derzeit unterstützt Hubzilla die Zugriffskontrolle für viele Datentypen, darunter Diskussionsbeiträge und -kommentare, Fotoalben, Veranstaltungen, Cloud-Dateien, Webseiten, Wikis und mehr. Jedes Objekt und die Art und Weise, wie und mit wem es geteilt wird, unterliegt vollständig Ihrer Kontrolle.

Diese Art der Kontrolle ist bei großen Unternehmensanbietern trivial, da sie die Benutzerdatenbank besitzen. Im Grid brauchen Sie keine riesige Benutzerdatenbank auf Ihrem Rechner - denn das Grid **ist** Ihre Benutzerdatenbank. Sie verfügt über eine im Grunde unendliche Kapazität (begrenzt durch die Gesamtzahl der Hubs, die im Internet online sind) und ist auf Hunderte, möglicherweise sogar Millionen von Computern verteilt. 

Der Zugang kann für jede Ressource, jeden Kanal oder jede Gruppe von Kanälen gewährt oder verweigert werden - überall im Grid. Andere können auf Ihre Inhalte zugreifen, wenn Sie es ihnen erlauben, und sie müssen nicht einmal ein Konto in Ihrem Hub haben.