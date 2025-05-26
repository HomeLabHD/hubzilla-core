### Hooks

Hooks ermöglichen es Plugins/Addons, sich an vielen Stellen in den Code „einzuhaken“ und das Verhalten zu ändern oder anderweitig unabhängige Aktionen durchzuführen, wenn eine Aktivität stattfindet oder auf bestimmte Datenstrukturen zugegriffen wird. Es gibt viele Hooks, die es Ihnen ermöglichen, sich an fast jeder Stelle in die Software einzuklinken und etwas anderes zu tun als das, was standardmäßig vorgesehen ist. Diesen Hooks werden zwei Variablen übergeben. Die erste ist die App-Struktur, die Details über den gesamten Zustand der Seitenanforderung enthält, während wir die resultierende Seite aufbauen. Die zweite ist eindeutig für den spezifischen Hook, der aufgerufen wird, und liefert spezifische Details darüber, was in der Software zum Zeitpunkt des Aufrufs des Hooks passiert.

[Erstellter Index aller Hooks und der Dateien, die sie aufrufen](/help/de/hooks)

[module_mod_aftercontent](/help/de/hook/module_mod_aftercontent)
Allgemeiner Hook für jedes Modul, ausgeführt nach mod_content(). Ersetzen Sie „module“ durch den Namen des Moduls, z. B. „photos_mod_aftercontent“.

[module_mod_content](/help/de/hook/module_mod_content)
Allgemeiner Hook für ein beliebiges Modul, wird vor mod_content() ausgeführt. Ersetzen Sie 'module' durch den Modulnamen, z. B. 'photos_mod_content'.

[module_mod_init](/help/de/hook/module_mod_init)
Allgemeiner Hook für ein beliebiges Modul, wird vor mod_init() ausgeführt. Ersetzen Sie 'module' durch den Modulnamen, z. B. 'photos_mod_init'.

[module_mod_post](/help/de/hook/module_mod_post)
Allgemeiner Hook für ein beliebiges Modul, der vor mod_post() ausgeführt wird. Ersetzen Sie 'module' durch den Namen des Moduls, z. B. 'photos_mod_post'.

[about_hook](/help/de/hook/about_hook)
Aufgerufen von der Seite siteinfo

[accept_follow](/help/de/hook/accept_follow)
Wird aufgerufen, wenn eine Verbindung akzeptiert wird (Freundschaftsanfrage)

[account_downgrade](/help/de/hook/account_downgrade)
Wird aufgerufen, wenn ein Konto abgelaufen ist, was auf eine mögliche Herabstufung auf die Serviceklasse „basic“ hinweist

[Konto_Einstellungen](/help/de/hook/account_settings)
Wird bei der Erstellung des Formulars für die Kontoeinstellungen aufgerufen

[account_settings_post](/help/de/hook/account_settings_post)
Wird bei der Buchung aus dem Kontoeinstellungsformular aufgerufen

[tätigkeit_filter](/help/de/hook/activity_filter)
Wird bei der Erstellung der Liste der Filter für die Netzwerkseite aufgerufen

[activity_mapper](/help/de/hook/activity_filter)
Wird bei der Bestimmung der Vorgangsart für die Übertragung aufgerufen.

[activity_decode_mapper](/help/de/hook/activity_filter)
Wird aufgerufen, wenn die Vorgangsart für die Übertragung bestimmt wird.

[activity_obj_mapper](/help/de/hook/activity_filter)
Wird aufgerufen, wenn der Objekttyp für die Übertragung bestimmt wird.

[activity_obj_decode_mapper](/help/de/hook/activity_filter)
Wird bei der Bestimmung des Objekttyps für die Übertragung aufgerufen.

[activity_order](/help/de/hook/activity_order)
Wird bei der Generierung der Liste der Bestelloptionen für die Netzseite aufgerufen

[addon_app_installed_filter](/help/de/hook/addon_app_installed_filter)
Wird aufgerufen, wenn festgestellt wird, ob eine addon_app installiert ist

[activity_received](/help/de/hook/activity_received)
Wird aufgerufen, wenn eine Aktivität (Beitrag, Kommentar, Like, etc.) von einer Nomad-Quelle empfangen wurde

[admin_aside](/help/de/hook/admin_aside)
Wird aufgerufen, wenn das Seitenleisten-Widget der Verwaltungsseite erzeugt wird

[affinity_labels](/help/de/hook/affinity_labels)
Wird verwendet, um alternative Beschriftungen für den Affinitätsslider zu generieren.

[api_perm_is_allowed](/help/de/hook/api_perm_is_allowed)
Wird aufgerufen, wenn perm_is_allowed() von einem API-Aufruf ausgeführt wird.

[app_destroy](/help/de/hook/app_destroy)
Wird aufgerufen, wenn eine App gelöscht wird.

[app_installed_filter](/help/de/hook/app_installed_filter)
Wird aufgerufen, wenn festgestellt wird, ob eine App installiert ist

[app_menu](/help/de/hook/app_menu)
Wird bei der Erstellung des app_menu Dropdowns aufgerufen (kann veraltet sein)

[attach_delete](/help/de/hook/attach_delete)
Wird aufgerufen, wenn Anhänge aus der Tabelle attach gelöscht werden

[atom_author](/help/de/hook/atom_author)
Wird aufgerufen, wenn ein Autor- oder Eigentümer-Element für einen Atom ActivityStream-Feed erzeugt wird

[atom_entry](/help/de/hook/atom_entry)
Wird bei der Erzeugung jedes Eintrags eines Atom ActivityStreams Feeds aufgerufen

[atom_feed](/help/de/hook/atom_feed)
Wird bei der Generierung eines Atom ActivityStreams Feeds aufgerufen

[atom_feed_end](/help/de/hook/atom_feed_end)
Wird aufgerufen, wenn die Erzeugung eines Atom ActivityStreams Feeds abgeschlossen ist

[attach_upload_file](/help/de/hook/attach_upload_file)
Wird beim Hochladen einer Datei aufgerufen

[authentifizieren](/help/de/hook/authenticate)
Kann alternative Authentifizierungsmechanismen bereitstellen

[author_is_pmable](/help/de/hook/author_is_pmable)
Wird aus dem Aktionsmenü des Threads aufgerufen, um festzustellen, ob wir dem Verfasser des Beitrags eine private E-Mail schicken können

[bb2diaspora](/help/de/hook/bb2diaspora)
Wird bei der Umwandlung von bbcode in Markdown aufgerufen

[bbcode](/help/de/hook/bbcode)
Wird am Ende der Konvertierung von bbcode in HTML aufgerufen

[bbcode_filter](/help/de/hook/bbcode_filter)
Wird zu Beginn der Umwandlung von bbcode in HTML aufgerufen

[bb_translate_video](/help/de/hook/bb_translate_video)
Wird aufgerufen, wenn eingebettete Dienste aus bbcode-Videoelementen extrahiert werden (wird selten verwendet)

[build_pagehead](/help/de/hook/build_pagehead)
Wird bei der Erstellung des HTML-Seitenkopfes aufgerufen

[can_comment_on_post](/help/de/hook/can_comment_on_post)
Wird aufgerufen, wenn entschieden wird, ob ein Kommentarfeld für einen Beitrag angezeigt werden soll oder nicht

[change_channel](/help/de/hook/change_channel)
Wird aufgerufen, wenn man sich bei einem Channel anmeldet (entweder während des Logins oder danach über den Channelmanager)

[channel_remove](/help/de/hook/channel_remove)
Wird aufgerufen, wenn ein Channel entfernt wird

[channel_links](/help/de/hook/channel_links)
Wird bei der Generierung des Link: HTTP-Header für einen Kanal

[channel_settings](/help/de/hook/channel_settings)
Wird aufgerufen, wenn die Seite mit den Channel-Einstellungen angezeigt wird

[chat_message](/help/de/hook/chat_message)
Wird aufgerufen, um eine Chat-Nachricht zu erstellen.

[chat_post](/help/de/hook/chat_post)
Wird aufgerufen, wenn eine Chat-Nachricht gepostet wurde.

[check_account_email](/help/de/hook/check_account_email)
Überprüft die bei einer Kontoregistrierung angegebene E-Mail

[check_account_invite](/help/de/hook/check_account_invite)
Validierung eines Einladungscodes bei der Verwendung von Website-Einladungen

[check_account_password](/help/de/hook/check_account_password)
Dient der Kontrolle von Kontopasswörtern (Mindestlänge, Einbeziehung von Zeichensätzen usw.)

[check_channelallowed](/help/de/hook/check_channelallowed)
Wird verwendet, um die Sperrlisten für schwarze und weiße Kanäle außer Kraft zu setzen oder zu umgehen.

[check_siteallowed](/help/de/hook/check_siteallowed)
Wird verwendet, um die schwarzen/weißen Sperrlisten für Websites außer Kraft zu setzen oder zu umgehen.

[collect_public_recipients](/help/de/hook/collect_public_recipients)
Wird verwendet, um eine Liste von Empfängern zu erstellen, an die eine öffentliche Nachricht gesendet werden soll.

[comment_buttons](/help/de/hook/comment_buttons)
Wird aufgerufen, wenn die Bearbeitungsschaltflächen für Kommentare angezeigt werden.

[comments_are_now_closed](/help/de/hook/comments_are_now_closed)
Wird aufgerufen, wenn entschieden wird, ob ein Kommentarfeld für einen Beitrag angezeigt werden soll oder nicht

[connect_premium](/help/de/hook/connect_premium)
Wird aufgerufen, wenn eine Verbindung zu einem Premium-Kanal hergestellt wird

[connection_remove](/help/de/hook/connection_remove)
Wird aufgerufen, wenn eine Verbindung gelöscht/entfernt wird

[connector_settings](/help/de/hook/connector_settings)
Wird aufgerufen, wenn die Seite mit den Features/Addon-Einstellungen aufgerufen wird

[construct_page](/help/de/hook/construct_page)
Allgemeiner Hook zur Bereitstellung von Inhalten für bestimmte Seitenbereiche. Wird aufgerufen, wenn die Comanche-Seite erstellt wird.

[kontakt_block_ende](/help/de/hook/contact_block_end)
Wird bei der Erstellung des „Connections“-Widgets in der Seitenleiste aufgerufen

[kontakt_edit](/help/de/hook/contact_edit)
Wird bei der Bearbeitung einer Verbindung über connedit aufgerufen

[kontakt_edit_post](/help/de/hook/contact_edit_post)
Wird aufgerufen, wenn ein Beitrag an connedit gesendet wird

[Kontakt_Auswahl_Optionen](/help/de/hook/contact_select_options)
Veraltet/unbenutzt

[content_security_policy](/help/de/hook/content_security_policy)
Wird vor der Ausgabe des Content-Security-Policy-Headers aufgerufen

[conversation_start](/help/de/hook/conversation_start)
Wird zu Beginn des Renderns einer Konversation (Nachricht oder Nachrichtensammlung oder Stream) aufgerufen

[cover_photo_content_end](/help/de/hook/cover_photo_content_end)
Wird aufgerufen, nachdem ein Titelbild hochgeladen wurde

[create_identity](/help/de/hook/create_identity)
Wird bei der Erstellung eines Channels aufgerufen

[cron](/help/de/hook/cron)
Wird aufgerufen, wenn eine geplante Aufgabe (Poller) ausgeführt wird

[cron_daily](/help/de/hook/cron_daily)
Wird aufgerufen, wenn täglich geplante Aufgaben ausgeführt werden

[cron_weekly](/help/de/hook/cron_weekly)
Wird aufgerufen, wenn wöchentlich geplante Aufgaben ausgeführt werden

[crypto_methods](/help/de/hook/crypto_methods)
Wird aufgerufen, wenn eine Liste von Kryptoalgorithmen in der lokal bevorzugten Reihenfolge erstellt wird

[daemon_addon](/help/de/hook/daemon_addon)
Wird aufgerufen, wenn der erweiterbare Hintergrund-Daemon aufgerufen wird

[daemon_master_release](/help/de/hook/daemon_master_release)
Wird zu Beginn der Verarbeitung von \Zotlabs\Daemon\Master::Release() aufgerufen

[directory_item](/help/de/hook/directory_item)
Wird beim Erzeugen einer Verzeichnisliste für die Anzeige aufgerufen

[discover_channel_webfinger](/help/de/hook/discover_channel_webfinger)
Wird aufgerufen, wenn ein Webfinger-Lookup durchgeführt wird

[display_item](/help/de/hook/display_item)
Wird für jedes Element aufgerufen, das in einem Gesprächsfaden angezeigt wird

[display_settings](/help/de/hook/display_settings)
Wird vom Einstellungsmodul aufgerufen, wenn der Abschnitt 'Anzeigeeinstellungen' angezeigt wird

[display_settings_post](/help/de/hook/display_settings_post)
Wird aufgerufen, wenn ein Beitrag aus dem Formular „Einstellungen anzeigen“ des Einstellungsmoduls angezeigt wird

[donate_contributors](/help/de/hook/donate_contributors)
Wird vom 'donate'-Addon aufgerufen, wenn eine Liste von Spendenempfängern erstellt wird

[donate_plugin](/help/de/hook/donate_plugin)
wird vom 'donate'-Addon aufgerufen

[donate_sponsoren](/help/de/hook/donate_sponsors)
aufgerufen durch das 'donate'-Addon

[dreport_ist_storable](/help/de/hook/dreport_is_storable)
wird vor dem Speichern eines Dreport-Datensatzes aufgerufen, um festzustellen, ob er gespeichert werden soll

[dreport_process](/help/de/hook/dreport_process)
wird für jeden gültigen Lieferbericht aufgerufen

[dropdown_extras](/help/de/hook/dropdown_extras)
Hinzufügen zusätzlicher Elemente zum Dropdown-Menü, wenn Element/Threads angezeigt werden.

[drop_item](/help/de/hook/drop_item)
wird aufgerufen, wenn ein 'item' entfernt wird

[encode_object](/help/de/hook/encode_object)
wird aufgerufen, wenn ein Objekt für die Übertragung kodiert wird.

[enotify](/help/de/hook/enotify)
wird vor jeder Benachrichtigung aufgerufen

[enotify_mail](/help/de/hook/enotify_mail)
wird aufgerufen, wenn eine Benachrichtigungs-E-Mail gesendet wird

[enotify_store](/help/de/hook/enotify_store)
wird beim Speichern eines Benachrichtigungsdatensatzes aufgerufen

[enotify_store_end](/help/de/hook/enotify_store_end)
wird aufgerufen, nachdem ein Benachrichtigungsdatensatz gespeichert wurde

[event_created](/help/de/hook/event_created)
wird aufgerufen, wenn ein Ereignisdatensatz erstellt wird

[event_store_event](/help/de/hook/event_store_event)
wird aufgerufen, wenn ein Ereignisdatensatz erstellt oder aktualisiert wird

[event_updated](/help/de/hook/event_updated)
wird aufgerufen, wenn ein Ereignisdatensatz geändert wird

[externals_url_select](/help/de/hook/externals_url_select)
wird aufgerufen, wenn eine Liste mit zufälligen Websites erstellt wird, von denen öffentliche Beiträge abgerufen werden sollen

[feature_enabled](/help/de/hook/feature_enabled)
wird aufgerufen, wenn 'feature_enabled()' verwendet wird

[merkmal_settings](/help/de/hook/feature_settings)
wird von der Einstellungsseite aufgerufen, wenn man 'addon/feature settings' besucht

[feature_settings_post](/help/de/hook/feature_settings_post)
wird von der Einstellungsseite aufgerufen, wenn von 'addon/feature settings' aus gepostet wird

[fetch_and_store](/help/de/hook/fetch_and_store)
wird aufgerufen, um das Filtern von 'entschlüsselten' Elementen vor der Speicherung zu ermöglichen.

[file_thumbnail](/help/de/hook/file_thumbnail)
wird aufgerufen, wenn Miniaturbilder für die Wolkenseite im Modus „Kacheln anzeigen“ erzeugt werden

[folgen](/help/de/hook/follow)
wird aufgerufen, wenn eine Follow-Operation stattfindet

[follow_from_feed](/help/de/hook/follow_from_feed)
wird aufgerufen, wenn eine Follow-Operation in einem RSS-Feed stattfindet

[follow_allow](/help/de/hook/follow_allow)
wird aufgerufen, bevor die Ergebnisse einer Follow-Operation gespeichert werden

[gender_selector](/help/de/hook/gender_selector)
wird bei der Erstellung der Dropdown-Liste „Geschlecht“ aufgerufen (erweitertes Profil)

[gender_selector_min](/help/de/hook/gender_selector_min)
wird bei der Erstellung der Dropdown-Liste „Geschlecht“ aufgerufen (normales Profil)

[generate_map](/help/de/hook/generate_map)
wird aufgerufen, um den HTML-Code für die Anzeige eines Orts auf der Karte nach Koordinaten zu erzeugen

[generate_named_map](/help/de/hook/generate_named_map)
wird aufgerufen, um die HTML-Datei für die Anzeige eines Kartenorts anhand eines Textes zu erzeugen

[get_all_api_perms](/help/de/hook/get_all_api_perms)
Wird aufgerufen, wenn die Berechtigungen für API-Verwendungen abgerufen werden

[get_all_perms](/help/de/hook/get_all_perms)
wird aufgerufen, wenn get_all_perms() verwendet wird

[get_best_language](/help/de/hook/get_best_language)
wird aufgerufen, wenn die bevorzugte Sprache für die Seite ausgewählt wird

[get_default_export_sections](/help/de/hook/get_default_export_sections)
Wird aufgerufen, um die Standardliste der zu exportierenden Funktionsdatengruppen in identity_basic_export() zu erhalten

[get_features](/help/de/hook/get_features)
Wird aufgerufen, wenn get_features() aufgerufen wird

[get_photo](/help/de/hook/get_photo)
Wird aufgerufen, wenn Fotoinhalte (außer Profilfotos) in mod_photo abgerufen werden

[get_profile_photo](/help/de/hook/get_profile_photo)
Wird aufgerufen, wenn der Inhalt des lokalen Profilfotos in mod_photo abgerufen wird

[get_role_perms](/help/de/hook/get_role_perms)
Wird aufgerufen, wenn get_role_perms() aufgerufen wird, um Berechtigungen für benannte Berechtigungsrollen zu erhalten

[global_permissions](/help/de/hook/global_permissions)
Wird aufgerufen, wenn die globale Berechtigungsliste erstellt wird

[home_content](/help/de/hook/home_content)
Wird von mod_home aufgerufen, um den Inhalt der Home-Seite zu ersetzen

[home_init](/help/de/hook/home_init)
Wird von der Funktion home_init() der Homepage aufgerufen

[hostxrd](/help/de/hook/hostxrd)
Wird bei der Erzeugung von .well-known/hosts-meta für „old webfinger“ aufgerufen (wird vom Diaspora-Protokoll verwendet)

[html2bb_video](/help/de/hook/html2bb_video)
Wird aufgerufen, wenn die html2bbcode-Übersetzung verwendet wird, um eingebettete Medien zu behandeln

[html2bbcode](/help/de/hook/html2bbcode)
Wird bei der Verwendung der html2bbcode-Übersetzung aufgerufen

[identität_basic_export](/help/de/hook/identity_basic_export)
Wird aufgerufen, wenn die Basisinformationen eines Channels zur Sicherung oder Übertragung exportiert werden.

[import_autor_xchan](/help/de/hook/import_author_xchan)
Wird aufgerufen, wenn ein Autor eines Beitrags mit xchan_hash gesucht wird, um sicherzustellen, dass er einen xchan-Eintrag auf unserer Website hat

[import_channel](/help/de/hook/import_channel)
Wird aufgerufen, wenn ein Kanal aus einer Datei oder einer API-Quelle importiert wird

[import_directory_profile](/help/de/hook/import_directory_profile)
Wird aufgerufen, wenn die Lieferung einer Profilstruktur aus einer externen Quelle verarbeitet wird (normalerweise für die Speicherung in Verzeichnissen)

[import_xchan](/help/de/hook/import_xchan)
Wird bei der Verarbeitung des Ergebnisses von zot_finger() aufgerufen, um das Ergebnis zu speichern

[item_photo_menu](/help/de/hook/item_photo_menu)
Wird aufgerufen, wenn die Liste der Aktionen erzeugt wird, die mit einem angezeigten Konversationselement verbunden sind

[item_store](/help/de/hook/item_store)
Wird aufgerufen, wenn item_store() einen Datensatz vom Typ item speichert

[item_stored](/help/de/hook/item_stored)
Wird aufgerufen, nachdem item_store() einen Datensatz des Typs item in der Datenbank gespeichert hat.

[item_custom](/help/de/hook/item_custom)
Wird aufgerufen, bevor item_store() einen Datensatz des Typs item speichert (damit Addons ITEM_TYPE_CUSTOM-Elemente verarbeiten können).

[item_store_update](/help/de/hook/item_store_update)
Wird aufgerufen, wenn item_store_update() aufgerufen wird, um einen gespeicherten Eintrag zu aktualisieren.

[item_stored_update](/help/de/hook/item_stored_update)
Wird aufgerufen, nachdem item_store_update() ein gespeichertes Element aktualisiert hat.

[item_translate](/help/de/hook/item_translate)
Wird von item_store und item_store_update aufgerufen, nachdem die Sprache des Beitrags automatisch erkannt wurde.

[jot_networks](/help/de/hook/jot_networks)
Wird aufgerufen, um die Liste der zusätzlichen Post-Plugins zu generieren, die aus dem ACL-Formular aktiviert werden sollen

[jot_tool](/help/de/hook/jot_tool)
Veraltet und möglicherweise überflüssig. Ermöglicht das Hinzufügen von Aktionsschaltflächen zum Beitragseditor.

[jot_tpl_filter](/help/de/hook/jot_tpl_filter)
Wird aufgerufen, um Vorlagenvariablen vor der Ersetzung in jot.tpl zu filtern.

[jot_header_tpl_filter](/help/de/hook/jot_header_tpl_filter)
Wird aufgerufen, um Vorlagenvariablen vor der Ersetzung in jot_header.tpl zu filtern.

[legal_webbie](/help/de/hook/legal_webbie)
Wird aufgerufen, um eine Kanaladresse zu validieren

[legal_webbie_text](/help/de/hook/legal_webbie_text)
Bietet eine Erklärung der Text-/Zeichenbeschränkungen für legal_webbie()

[load_pdl](/help/de/hook/load_pdl)
Wird aufgerufen, wenn wir eine PDL-Datei oder eine Beschreibung laden

[local_dir_update](/help/de/hook/local_dir_update)
Wird aufgerufen, wenn eine Verzeichnisaktualisierung von einem Channel auf dem Verzeichnisserver verarbeitet wird

[location_move](/help/de/hook/location_move)
Wird aufgerufen, wenn einem UNO-Channel ein neuer Standort mitgeteilt wurde (was auf eine Verschiebung und nicht auf einen Klon hinweist)

[protokolliert](/help/de/hook/logged_in)
Wird aufgerufen, wenn die Authentifizierung auf irgendeine Weise erfolgreich war

[Logger](/help/de/hook/logger)
Wird aufgerufen, wenn ein Eintrag in die Logdatei der Anwendung gemacht wird

[logging_out](/help/de/hook/logging_out)
Wird bei der Abmeldung aufgerufen

[login_hook](/help/de/hook/login_hook)
Wird bei der Generierung des Anmeldeformulars aufgerufen

[magic_auth](/help/de/hook/magic_auth)
Wird bei der Verarbeitung einer magic-auth-Sequenz aufgerufen

[markdown_to_bb](/help/de/hook/markdown_to_bb)
Wird bei der Verarbeitung der Markdown-Konvertierung aufgerufen

[match_webfinger_location](/help/de/hook/match_webfinger_location)
Wird bei der Verarbeitung von Webfinger-Anfragen aufgerufen

[magic_auth_openid_success](/help/de/hook/magic_auth_openid_success)
Wird aufgerufen, wenn ein magic-auth aufgrund von openid-Anmeldedaten erfolgreich war

[magic_auth_success](/help/de/hook/magic_auth_success)
Wird aufgerufen, wenn ein magic-auth erfolgreich war

[main_slider](/help/de/hook/main_slider)
Wird bei der Generierung des Affinitätswerkzeugs aufgerufen

[marital_selector](/help/de/hook/marital_selector)
Wird aufgerufen, wenn die Auswahlliste für das Dropdown-Menü des Profils „Familienstand“ erstellt wird (erweitertes Profil)

[marital_selector_min](/help/de/hook/marital_selector_min)
Wird bei der Erstellung der Auswahlliste für das Dropdown-Profil „Familienstand“ aufgerufen (normales Profil)

[module_loaded](/help/de/hook/module_loaded)
Wird aufgerufen, wenn ein Modul erfolgreich für eine URL-Anfrage auf dem Server lokalisiert wurde.

[mood_verbs](/help/de/hook/mood_verbs)
Wird bei der Erstellung der Liste der Stimmungen aufgerufen

[nav](/help/de/hook/nav)
Wird bei der Erstellung der Navigationsleiste aufgerufen

[network_content_init](/help/de/hook/network_content_init)
Wird beim Laden des Inhalts für die Netzwerkseite aufgerufen

[netzwerk_ping](/help/de/hook/network_ping)
Wird bei einer Ping-Anfrage aufgerufen

[netzwerk_zu_name](/help/de/hook/network_to_name)
Veraltet

[notifier_end](/help/de/hook/notifier_end)
Wird aufgerufen, wenn eine Zustellschleife abgeschlossen ist

[notifier_hub](/help/de/hook/notifier_hub)
Wird aufgerufen, wenn ein Hub zugestellt wurde

[notifier_normal](/help/de/hook/notifier_normal)
Wird aufgerufen, wenn der Notifizierer für eine 'normale' Zustellung aufgerufen wird

[notifier_process](/help/de/hook/notifier_process)
Wird aufgerufen, wenn der Notifizierende eine Nachricht/Ereignis verarbeitet

[obj_verbs](/help/de/hook/obj_verbs)
Wird bei der Erstellung der Liste der für das Profil „Dinge“ verfügbaren Verben aufgerufen.

[oembed_action](/help/de/hook/oembed_action)
Wird aufgerufen, wenn entschieden wird, ob eine Oembed-Url gefiltert, blockiert oder genehmigt werden soll

[oembed_probe](/help/de/hook/oembed_probe)
Wird aufgerufen, wenn eine Suche nach Oembed-Inhalten durchgeführt wird.

[other_encapsulate](/help/de/hook/other_encapsulate)
Wird aufgerufen, wenn Inhalte verschlüsselt werden, für die der Algorithmus unbekannt ist (siehe auch crypto_methods)

[other_unencapsulate](/help/de/hook/other_unencapsulate)
Wird aufgerufen, wenn Inhalte entschlüsselt werden, deren Algorithmus unbekannt ist (siehe auch crypto_methods)

[page_content_top](/help/de/hook/page_content_top)
Wird aufgerufen, wenn wir eine Webseite generieren (vor dem Aufruf der Modul-Content-Funktion)

[page_end](/help/de/hook/page_end)
Wird aufgerufen, nachdem wir den Seiteninhalt generiert haben

[page_header](/help/de/hook/page_header)
Wird bei der Generierung der Navigationsleiste aufgerufen

[page_meta](/help/de/hook/page_header)
Wird bei der Generierung der Metadaten im Seitenkopf aufgerufen.

[parse_atom](/help/de/hook/parse_atom)
Wird aufgerufen, wenn ein Atom/RSS-Feed-Element geparst wird.

[parse_link](/help/de/hook/parse_link)
Wird aufgerufen, wenn eine URL abgefragt wird, um daraus einen Beitrag zu generieren

[pdl_selector](/help/de/hook/pdl_selector)
Wird bei der Erstellung einer Layoutauswahl in einem Formular aufgerufen

[perm_is_allowed](/help/de/hook/perm_is_allowed)
Wird während perm_is_allowed() aufgerufen, um festzustellen, ob eine Berechtigung für diesen Kanal und Beobachter erlaubt ist

[permissions_create](/help/de/hook/permissions_create)
Wird aufgerufen, wenn ein Bucheintrag (Verbindung) erstellt wird

[permissions_update](/help/de/hook/permissions_update)
Wird aufgerufen, wenn eine Berechtigungsaktualisierung übertragen wird

[permit_hook](/help/de/hook/permit_hook)
Wird aufgerufen, bevor ein registrierter Hook tatsächlich ausgeführt wird, um festzustellen, ob er erlaubt oder blockiert werden soll

[personal_xrd](/help/de/hook/personal_xrd)
Wird bei der Generierung der persönlichen XRD für „old webfinger“ (Diaspora) aufgerufen

[photo_post_end](/help/de/hook/photo_post_end)
Wird nach dem Hochladen eines Fotos aufgerufen

[photo_upload_begin](/help/de/hook/photo_upload_begin)
Wird aufgerufen, wenn versucht wird, ein Foto hochzuladen

[photo_upload_end](/help/de/hook/photo_upload_end)
Wird aufgerufen, wenn ein Foto-Upload verarbeitet wurde

[photo_upload_file](/help/de/hook/photo_upload_file)
Wird aufgerufen, um alternative Dateinamen für einen Upload zu generieren

[photo_upload_form](/help/de/hook/photo_upload_form)
Wird aufgerufen, wenn ein Foto-Upload-Formular generiert wird

[photo_view_filter](/help/de/hook/photo_view_filter)
Wird aufgerufen, bevor die Daten an die photo_view-Vorlage übergeben werden

[poke_verbs](/help/de/hook/poke_verbs)
Wird bei der Erstellung der Liste der Aktionen für das Modul „poke“ aufgerufen

[post_local](/help/de/hook/post_local)
Wird aufgerufen, wenn ein Artikel auf diesem Rechner über mod/item.php eingestellt wurde (auch über API)

[post_local_end](/help/de/hook/post_local_end)
Wird aufgerufen, wenn ein lokaler Postvorgang abgeschlossen ist

[post_local_start](/help/de/hook/post_local_start)
Wird aufgerufen, wenn ein lokaler Postvorgang beginnt

[post_mail](/help/de/hook/post_mail)
Wird aufgerufen, wenn eine Mail-Nachricht verfasst wurde

[post_mail_end](/help/de/hook/post_mail_end)
Wird aufgerufen, wenn eine Mail-Nachricht zugestellt wurde

[post_remote](/help/de/hook/post_remote)
Wird aufgerufen, wenn eine Aktivität von einem anderen Standort eintrifft

[post_remote_end](/help/de/hook/post_remote_end)
Wird nach der Verarbeitung einer Remote-Post aufgerufen

[post_remote_update](/help/de/hook/post_remote_update)
Wird bei der Verarbeitung eines entfernten Beitrags aufgerufen, der eine Bearbeitung oder Aktualisierung beinhaltet

[post_remote_update_end](/help/de/hook/post_remote_update_end)
Wird nach der Verarbeitung eines entfernten Beitrags aufgerufen, der eine Bearbeitung oder Aktualisierung beinhaltete

[prepare_body](/help/de/hook/prepare_body)
Wird aufgerufen, wenn der HTML-Code für ein angezeigtes Konversationsobjekt generiert wird.

[prepare_body_final](/help/de/hook/prepare_body_final)
Wird nach der Generierung des HTML für ein angezeigtes Konversationselement aufgerufen

[prepare_body_init](/help/de/hook/prepare_body_init)
Wird vor der Generierung des HTML für ein angezeigtes Konversationselement aufgerufen

[privacygroup_extras](/help/de/hook/privacygroup_extras)
Wird vor der Generierung des HTML für die Bearbeitungsoptionen der Privacy Group aufgerufen

[privacygroup_extras_delete](/help/de/hook/privacygroup_extras_delete)
Wird aufgerufen, nachdem die Privatsphärengruppe gelöscht wurde.

[privacygroup_extras_post](/help/de/hook/privacygroup_extras_post)
Wird aufgerufen, wenn das Formular zur Bearbeitung der Privatsphärengruppe abgeschickt wird.

[proc_run](/help/de/hook/proc_run)
Wird beim Aufruf von PHP-Unterprozessen aufgerufen

[process_channel_sync_delivery](/help/de/hook/process_channel_sync_delivery)
Wird aufgerufen, wenn ein 'Sync-Paket' mit Struktur- und Tabellenaktualisierungen von einem Channel-Clone angenommen wird.

[profile_advanced](/help/de/hook/profile_advanced)
Wird bei der Generierung einer erweiterten Profilseite aufgerufen

[profil_edit](/help/de/hook/profile_edit)
Wird bei der Bearbeitung eines Profils aufgerufen

[profil_foto_inhalt_ende](/help/de/hook/profile_photo_content_end)
Wird bei der Änderung eines Profilfotos aufgerufen

[profil_post](/help/de/hook/profile_post)
Wird beim Posten eines bearbeiteten Profils aufgerufen

[profile_sidebar](/help/de/hook/profile_sidebar)
Wird aufgerufen, wenn die „Kanal-Seitenleiste“ oder das Miniprofil erstellt wird

[profile_sidebar_enter](/help/de/hook/profile_sidebar_enter)
Wird vor der Erstellung der 'Channel Sidebar' oder des Miniprofils aufgerufen

[queue_deliver](/help/de/hook/queue_deliver)
Wird aufgerufen, wenn eine Nachricht in der Warteschlange zugestellt wird

[register_account](/help/de/hook/register_account)
Wird aufgerufen, wenn ein Konto erstellt worden ist

[render_location](/help/de/hook/render_location)
Wird aufgerufen, um eine ineraktive Inline-Map zu erzeugen

[replace_macros](/help/de/hook/replace_macros)
Wird vor dem Aufrufen des Vorlagenprozessors aufgerufen

[reverse_magic_auth](/help/de/hook/reverse_magic_auth)
Wird vor dem Aufruf von reverse magic auth aufgerufen, um Sie auf Ihre eigene Website zu schicken, damit Sie sich auf dieser Website authentifizieren können

[Einstellungen_Konto](/help/de/hook/settings_account)
Wird bei der Erstellung des Formulars für die Kontoeinstellungen aufgerufen

[einstellungen_form](/help/de/hook/settings_form)
Wird bei der Erstellung des Formulars für die Channel-Einstellungen aufgerufen

[einstellungen_post](/help/de/hook/settings_post)
Wird bei der Buchung aus dem Formular für die Kanaleinstellungen aufgerufen

[sexpref_selector](/help/de/hook/sexpref_selector)
Wird aufgerufen, wenn ein Dropdown-Menü für sexuelle Präferenzen erstellt wird (erweitertes Profil)

[sexpref_selector_min](/help/de/hook/sexpref_selector_min)
Wird aufgerufen, wenn eine Auswahlliste der sexuellen Präferenzen erstellt wird (normales Profil)

[smilie](/help/de/hook/smilie)
Wird beim Übersetzen von Emoticons aufgerufen

[status_editor](/help/de/hook/status_editor)
Wird bei der Erstellung des status_editor aufgerufen.

[stream_item](/help/de/hook/stream_item)
Wird für jedes Element aufgerufen, das über conversation() zur Anzeige gerendert wird

[system_app_installed_filter](/help/de/hook/system_app_installed_filter)
Wird aufgerufen, wenn festgestellt wird, ob eine System-App installiert ist.

[tagged](/help/de/hook/tagged)
Wird aufgerufen, wenn eine Lieferung verarbeitet wird, die dazu führt, dass Sie getaggt werden

[thumbnail](/help/de/hook/thumbnail)
Wird beim Erzeugen von Miniaturbildern für die Kachelansicht des Cloud-Speichers aufgerufen

[update_unseen](/help/de/hook/update_unseen)
Wird vor dem automatischen Markieren von im Browser geladenen Sendungen, die gesehen wurden, aufgerufen

[validate_channelname](/help/de/hook/validate_channelname)
Wird verwendet, um die von einem Kanal verwendeten Namen zu validieren

[webfinger](/help/de/hook/webfinger)
Wird beim Besuch des Dienstes webfinger (RFC7033) aufgerufen

[well_known](/help/de/hook/well_known)
Wird beim Zugriff auf die speziellen '.well-known'-Site-Adressen aufgerufen

[wiki_preprocess](/help/de/hook/wiki_preprocess)
Wird aufgerufen, bevor Markdown-/Bbcode-Prozessoren für Wikiseiten ausgeführt werden

[zot_best_algorithm](/help/de/hook/zot_best_algorithm)
Wird bei der Aushandlung von Verschlüsselungsalgorithmen mit entfernten Sites aufgerufen

[zid](/help/de/hook/zid)
Wird aufgerufen, wenn die zid des Beobachters zu einer URL hinzugefügt wird

[zid_init](/help/de/hook/zid_init)
Wird bei der Authentifizierung eines Besuchers aufgerufen, der zid verwendet hat

[zot_finger](/help/de/hook/zot_finger)
Wird aufgerufen, wenn ein Nomad-Infopaket angefordert wurde (dies ist unser Webfinger-Erkennungsmechanismus)
