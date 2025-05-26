### Hubzilla-Entwicklung - einige nützliche Grundfunktionen 

- `get_account_id()`

Gibt die numerische account_id zurück, wenn authentifiziert oder 0. Es ist möglich, authentifiziert und nicht mit einem Channel verbunden zu sein.

- `local_channel()`

Gibt die numerische channel_id zurück, wenn sie authentifiziert und mit einem Channel verbunden ist, oder 0. Wird im Code manchmal als $uid bezeichnet.

- `remote_channel()`

Gibt einen authentifizierten String-Hash des Red Global Identifier zurück, wenn er über Remote-Auth authentifiziert wurde, oder einen leeren String.

- `App::get_observer()`

gibt eine xchan-Struktur zurück, die den aktuellen Betrachter repräsentiert, wenn er authentifiziert ist (lokal oder remote).

- `get_config($family,$key), get_pconfig($uid,$family,$key)`

Gibt die Konfigurationseinstellung für $family und $key zurück oder false, wenn sie nicht gesetzt ist.
Veraltet: Verwenden Sie stattdessen `Zotlabs\Lib\Config::Get`.

- `set_config($family,$key,$value), set_pconfig($uid,$family,$key,$value)`

Setzt den Wert der Konfigurationseinstellung für $family und $key auf $value. Gibt $value zurück. Die config-Versionen arbeiten mit systemweiten Einstellungen. Die pconfig-Versionen erhalten/setzen die Werte für eine bestimmte Integer uid (channel_id).
Veraltet: Verwenden Sie stattdessen `Zotlabs\Lib\Config::Set`.

- `dbesc()`

Escape-Zeichenfolgen, die in DB-Abfragen verwendet werden. Diese Funktion gibt die escapte Zeichenkette zurück. Ganzzahlige DB-Parameter sollten alle mit intval() in Ganzzahlen umgewandelt werden

- `q($sql,$var1...)`

Führt eine DB-Abfrage mit der SQL-Anweisung $sql durch. Die printf-artigen Argumente %s und %d werden durch variable Argumente ersetzt, die jeweils entsprechend dbesc() oder intval() sein sollten. SELECT-Abfragen geben ein Array von Ergebnissen oder false zurück, wenn ein SQL- oder DB-Fehler vorliegt. Andere Abfragen geben true zurück, wenn der Befehl erfolgreich war, oder false, wenn er nicht erfolgreich war.

- `t($Zeichenfolge)`

Gibt die übersetzte Variante von $string für die aktuelle Sprache zurück oder $string (Standardsprache 'en'), wenn die Sprache nicht bekannt ist oder eine übersetzte Version des Strings nicht existiert.

- `x($var), $x($array,$key)`

Kurzer Test, um zu sehen, ob die Variable $var gesetzt und nicht leer ist. Die Tests variieren je nach Typ. Gibt false zurück, wenn $var oder $key nicht gesetzt ist. Wenn die Variable gesetzt ist, wird 1 zurückgegeben, wenn sie einen Wert ungleich Null hat, andernfalls wird 0 zurückgegeben. -- z.B. x('') oder x(0) gibt 0 zurück;