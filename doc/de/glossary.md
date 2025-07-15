### Glossar  

- **Hub**

  Eine Instanz dieser Software, die auf einem Standard-Webserver läuft.

- **Grid**

  Das globale Netzwerk von Hubs, die mit Hilfe des Zot-Protokolls Informationen untereinander austauschen.   

- **Kanal**

  Die grundlegende Identität im Grid. Ein Channel kann eine Person, einen Blog oder ein Forum repräsentieren, um nur einige zu nennen. Channels können Verbindungen mit anderen Channels herstellen, um Informationen mit sehr detaillierten Berechtigungen zu teilen.   

- **Klonen**

  Kanäle können Klone haben, die mit separaten und ansonsten nicht verbundenen Konten auf unabhängigen Hubs verbunden sind. Die mit einem Channel geteilte Kommunikation wird zwischen den Channel-Klonen synchronisiert, so dass ein Channel Nachrichten senden und empfangen und auf gemeinsame Inhalte von mehreren Hubs zugreifen kann. Dies bietet Ausfallsicherheit bei Netzwerk- und Hardwareausfällen, was für selbst gehostete oder mit begrenzten Ressourcen ausgestattete Webserver ein großes Problem darstellen kann. Das Klonen ermöglicht es Ihnen, einen Channel vollständig von einem Hub zu einem anderen zu verschieben und dabei Ihre Daten und Verbindungen mitzunehmen. Siehe nomadische Identität.   

- **nomadische Identität**

  Die Fähigkeit, eine Identität über unabhängige Hubs und Webdomänen hinweg zu authentifizieren und einfach zu migrieren. Die nomadische Identität bietet echte Eigentumsrechte an einer Online-Identität, da die Identitäten der Kanäle, die von einem Konto auf einem Hub kontrolliert werden, nicht an den Hub selbst gebunden sind. Ein Hub ist eher eine Art „Gastgeber“ für Kanäle. Bei Hubzilla haben Sie kein „Konto“ auf einem Server wie bei typischen Websites, sondern Sie besitzen eine Identität, die Sie über das Netz mitnehmen können, indem Sie Klone verwenden.   

- **Nomad**

  Das neuartige JSON-basierte Protokoll zur Implementierung sicherer dezentraler Kommunikation und Dienste. Es unterscheidet sich von vielen anderen Kommunikationsprotokollen, indem es die Kommunikation auf einem dezentralen Identitäts- und Authentifizierungsrahmen aufbaut. Die Authentifizierungskomponente ähnelt dem OpenID-Konzept, ist aber von DNS-basierten Identitäten isoliert. Soweit möglich, erfolgt die Fernauthentifizierung still und unsichtbar. Dies bietet einen Mechanismus für eine verteilte Zugangskontrolle im Internet, der unauffällig ist.
  
  Ursprünglich trug das Protokoll den Namen Zot. Im Jahr 2021 wurde es von Mike Macgirvin in Nomad umbenannt. Inzwischen wird zwischen dem Protokoll und der Implementierung (Software) des Protokolls unterschieden. Die Implementierung wird bei Hubzilla weiterhin Zot genannt (genauer Zot6, weil es die Implementierung des damals noch gleich benannten Protokolls in der Version 6 fortführt).
  
  Grundsätzlich wird das Protokoll nur noch als Nomad bezeichnet. Wenn der Begriff Zot oder Zot6 (meist in der Form "Nomad/Zot6") verwendet wird, ist, sofern es um das Protokoll geht, das Nomad-Protokoll gemeint. Zot bzw. Zot6 tauchen eigenständig nur noch im Bereich der Softwareentwicklung bei Hubzilla auf, weil die Routinen und die Programmbibliothek, welche Nomad in der Praxis umsetzen, diesen Namen tragen.
  
  <u>Hinweis:</u> Die Implementierungen von Nomad in Hubzilla und in (streams) sind in Teilen nicht miteinander kompatibel. Das betrifft insbesondere die nomadische Identität. So ist es nicht möglich, einen Hubzilla-Kanal (Nomad v. Zot6) auf einer (streams) Instanz (Nomad v, Zot12) zu klonen, und umgekehrt.
