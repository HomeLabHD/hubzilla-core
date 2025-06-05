### Datenbank

#### Datenbank-Updates  

Auf der Seite `/admin/dbsync` kann der Administrator überprüfen, ob eine Aktualisierung fehlgeschlagen ist, und sie gegebenenfalls erneut versuchen.

Wenn eine Aktualisierung fehlgeschlagen ist, aber aus irgendeinem Grund nicht als fehlgeschlagen registriert wird, kann der Administrator versuchen, die Aktualisierung erneut auszuführen. Zum Beispiel für die DB-Aktualisierung #1999, indem er die Webseite besucht:

`/admin/dbsync/1999`

#### Datenbank-Tabellen

| Tabelle                                                      | Beschreibung                                                 |
| ------------------------------------------------------------ | ------------------------------------------------------------ |
| [abconfig](/help/de/database/db_abconfig) | beliebiger Speicherplatz für Verbindungen von lokalen Kanälen |
| [abook](/help/de/database/db_abook)      | Verbindungen der lokalen Kanäle                              |
| [account](/help/de/database/db_account)  | Dienstanbieterkonto                                          |
| [addon](/help/de/database/db_addon)      | registrierte Plugins                                         |
| [app](/help/de/database/db_app)          | persönliche App-Daten                                        |
| [attach](/help/de/database/db_attach)    | Dateianhänge                                                 |
| [auth_codes](/help/de/database/db_auth_codes) | OAuth Benutzung                                              |
| [cache](/help/de/database/db_cache)      | OEmbed Cache                                                 |
| [cal](/help/de/database/db_cal)          | CalDAV-Container für Ereignisse                              |
| [channel](/help/de/database/db_channel)  | lokale Kanäle                                                |
| [chat](/help/de/database/db_chat)        | Chatraum-Inhalte                                             |
| [chatpresence](/help/de/database/db_chatpresence) | Kanalpräsenzinformationen für den Chat                       |
| [chatroom](/help/de/database/db_chatroom) | Daten für den eigentlichen Chatraum                          |
| [clients](/help/de/database/db_clients)  | OAuth Benutzung                                              |
| [config](/help/de/database/db_config)    | Hauptkonfigurationsspeicher                                  |
| [conv](/help/de/database/db_conv)        | Meta-Konversationsstruktur für private Nachrichten in Diaspora |
| [event](/help/de/database/db_event)      | Events                                                       |
| [pgrp_member](/help/de/database/db_pgrp_member) | Datenschutzgruppen (Sammlungen), Gruppeninformationen        |
| [pgrp](/help/de/database/db_pgrp)        | Datenschutzgruppen (Sammlungen), Mitgliederinformationen     |
| [hook](/help/de/database/db_hook)        | Plugin-Hook-Register                                         |
| [hubloc](/help/de/database/db_hubloc)    | xchan-Standortspeicher, verknüpft einen Hub-Standort mit einem xchan |
| [iconfig](/help/de/database/db_iconfig)  | erweiterbarer, beliebiger Speicher für Elemente              |
| [issue](/help/de/database/db_issue)      | künftige Fehler-/Problemdatenbank                            |
| [item](/help/de/database/db_item)        | alle Beiträge und Webseiten                                  |
| [item_id](/help/de/database/db_item_id)  | (veraltet durch iconfig) andere Identifikatoren in anderen Diensten für Beiträge |
| [likes](/help/de/database/db_likes)      | "Dinge“ mögen                                                |
| [mail](/help/de/database/db_mail)        | private Nachrichten                                          |
| [menu](/help/de/database/db_menu)        | Webseiten-Menü-Daten                                         |
| [menu_item](/help/de/database/db_menu_item) | Einträge für Menüs auf Webseiten                             |
| [notify](/help/de/database/db_notify)    | Benachrichtigungen                                           |
| [obj](/help/de/database/db_obj)          | Objektdaten für Dinge (x hat y)                              |
| [outq](/help/de/database/db_outq)        | Ausgangswarteschlange                                        |
| [pconfig](/help/de/database/db_pconfig)  | persönlicher (pro Kanal) Konfigurationsspeicher              |
| [photo](/help/de/database/db_photo)      | Fotospeicher                                                 |
| [poll](/help/de/database/db_poll)        | Daten für Umfragen                                           |
| [poll_elm](/help/de/database/db_poll_elm) | Daten für Abfrageelemente                                    |
| [profdef](/help/de/database/db_profdef)  | Definitionen für benutzerdefinierte Profilfelder             |
| [profext](/help/de/database/db_profext)  | benutzerdefinierte Profilfelddaten                           |
| [profile](/help/de/database/db_profile)  | Kanalprofile                                                 |
| [profile_check](/help/de/database/db_profile_check) | DFRN-Fernautorisierung, kann veraltet sein                   |
| [register](/help/de/database/db_register) | Registrierungen, die eine Verwaltungsgenehmigung erfordern   |
| [session](/help/de/database/db_session)  | Speicherung von Websitzungen                                 |
| [shares](/help/de/database/db_shares)    | Informationen über gemeinsame Elemente                       |
| [sign](/help/de/database/db_sign)        | Diaspora-Unterschriften. Wird schrittweise abgebaut.         |
| [site](/help/de/database/db_site)        | Standorttabelle zum Auffinden von Verzeichnis-Peers          |
| [source](/help/de/database/db_source)    | Daten aus Kanalquellen                                       |
| [sys_perms](/help/de/database/db_sys_perms) | erweiterbare Berechtigungen für OAuth                        |
| [term](/help/de/database/db_term)        | Tabelle der Artikeltaxonomie (Kategorien, Tags usw.)         |
| [tokens](/help/de/database/db_tokens)    | OAuth Benutzung                                              |
| [updates](/help/de/database/db_updates)  | Verzeichnis-Synchronisations-Updates                         |
| [verify](/help/de/database/db_verify)    | allgemeine Verifikationsstruktur                             |
| [vote](/help/de/database/db_vote)        | Abstimmungsdaten für Umfragen                                |
| [xchan](/help/de/database/db_xchan)      | Liste der bekannten Kanäle im Universum                      |
| [xchat](/help/de/database/db_xchat)      | Chaträume mit Lesezeichen                                    |
| [xconfig](/help/de/database/db_xconfig)  | wie pconfig, aber für Kanäle ohne lokales Konto              |
| [xign](/help/de/database/db_xign)        | von Freundschaftsvorschlägen ignorierte Kanäle               |
| [xlink](/help/de/database/db_xlink)      | von poco abgeleitete „Freunde von Freunden“-Verknüpfungen, auch Speicherung von Bewertungen |
| [xperm](/help/de/database/db_xperm)      | OAuth/OpenID-Connect erweiterbare Berechtigungen Berechtigungsspeicher |
| [xprof](/help/de/database/db_xprof)      | wenn dieser Knotenpunkt ein Verzeichnisserver ist, enthält er grundlegende öffentliche Profilinformationen über jeden im Netz |
| [xtag](/help/de/database/db_xtag)        | wenn dieser Hub ein Verzeichnisserver ist, enthält er Tags oder Interessen von jedem im Netzwerk |
