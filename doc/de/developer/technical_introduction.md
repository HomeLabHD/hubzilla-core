### Technische Einführung

Nomad ist ein JSON-basiertes Web-Framework zur Implementierung sicherer dezentraler Kommunikation und Dienste. Um diese Funktionalität bereitzustellen, erstellt Nomad eine dezentralisierte, weltweit eindeutige Kennung für jeden Knotenpunkt im Netz. Dieser globale Identifikator ist nicht untrennbar mit dem DNS verbunden, wodurch die erforderliche Mobilität gewährleistet wird. Viele der bestehenden dezentralen Kommunikationsrahmen bieten zwar den Kommunikationsaspekt, aber keine Fernzugriffskontrolle und Authentifizierung. Außerdem basieren die meisten dieser Systeme auf dem „Webfinger“, der die Identität immer noch an Domänennamen bindet und keine nomadische Identität unterstützen kann.

Die Hauptprobleme, die Nomad angeht, sind 

- vollständig dezentralisierte Kommunikation
- Unabhängigkeit von DNS-basierter Identität
- Knotenmobilität
- nahtlose Fernauthentifizierung
- hohe Leistung

Wir werden uns auf DNS-basierte user@host-Adressen als „benutzerfreundlichen“ Mechanismus stützen, um anderen mitzuteilen, wo man sich befindet, nämlich auf einem benannten Host mit einem bestimmten Benutzernamen, und die Kommunikation mit DNS-Entitäten wird über TCP und das Web abgewickelt.

Das zugrundeliegende Protokoll bietet jedoch eine Abstraktionsschicht darüber, so dass ein Kommunikationsknoten (z. B. „Identität“) zu einem anderen DNS-Standort wechseln kann und sich (nach bestem Wissen und Gewissen) von Standortwechseln erholt und/oder bereits bestehende Kommunikationsbeziehungen aufrechterhält. Ein Nebeneffekt dieser Anforderung ist die Möglichkeit, von alternativen/mehreren DNS-Standorten und Dienstanbietern aus zu kommunizieren und dennoch eine einzige Online-Identität beizubehalten.

Wir nennen dieses Overlay-Netz das „Grid“. Die an dieses Netz angeschlossenen Server werden als „Hubs“ bezeichnet und können eine beliebige Anzahl von individuellen Identitäten unterstützen. 

Eine Identität muss nicht unbedingt mit einer Person übereinstimmen. Sie ist lediglich etwas, das die Fähigkeit erfordert, innerhalb des Netzes zu kommunizieren. 

Die Fähigkeit zur Wiederherstellung wird durch Kommunikation mit dem ursprünglichen Standort erreicht, wenn eine neue oder Ersatzidentität erstellt wird, oder als Rückgriff auf eine gespeicherte Datei, die die Identität und ihre Kontakte beschreibt, für den Fall, dass der alte Standort nicht mehr antwortet. 

Zumindest kurzfristig hat die Mobilität bestehender Inhalte nicht die höchste Priorität. Dies kann zu einem späteren Zeitpunkt geschehen oder auch nicht. Die wichtigsten Dinge, die wir behalten wollen, sind Ihre Identität und Ihre Freunde.  

Adressen, die von mehreren Personen gemeinsam genutzt werden, sind user@host und beschreiben die **aktuellen** lokalen Zugangsdaten für eine bestimmte Identität. Es handelt sich um DNS-basierte Adressen, die als Seed verwendet werden, um eine bestimmte Identität innerhalb des Netzes zu lokalisieren. Die Maschinenkommunikation bindet diese Adresse an eine weltweit eindeutige ID. Eine einzelne, weltweit eindeutige ID kann an eine beliebige Anzahl von DNS-Standorten angehängt oder gebunden werden. Sobald eine Identität einem DNS-Standort zugeordnet oder an diesen gebunden ist, besteht die Kommunikation nur noch aus der Kenntnis der weltweit eindeutigen Adresse und des derzeit verwendeten DNS (url) (um die aktuelle Kommunikation zurückzurufen und zu überprüfen/abzuschließen). Diese Konzepte werden in Zukunft noch genauer spezifiziert. 

Damit eine Identität über Standorte hinweg bestehen bleibt, muss man in der Lage sein, Folgendes bereitzustellen oder wiederherzustellen 



- die weltweit eindeutige ID für diese Identität
- den dieser Identität zugewiesenen privaten Schlüssel
- (falls der ursprüngliche Server nicht mehr existiert) ein Adressbuch mit Kontakten für diese Identität.

Diese Informationen können vom ursprünglichen Server über eine API exportiert und/oder auf eine Festplatte oder einen USB-Stick heruntergeladen werden. 

Wir können auch versuchen, die Identität mit noch weniger Informationen wiederherzustellen, aber dies ist anfällig für Account-Hijacking und erfordert, dass Ihre Kontakte die Änderung bestätigen. 

Um eine hochleistungsfähige Kommunikation zu ermöglichen, ist das Datenübertragungsformat für alle Aspekte von Nomad JSON. XML-Kommunikation erfordert viel zu viel Overhead.

Die bidirektionale Verschlüsselung basiert auf RSA-Schlüsseln mit 4096 Bit, die im DER/ASN.1-Format unter Verwendung der PKCS#8-Kodierungsvariante ausgedrückt werden, mit AES-Verschlüsselung von Elementen mit variabler Länge oder großen Elementen. Die genauen Verschlüsselungsalgorithmen sind zwischen den Standorten verhandelbar. 

Einige Aspekte der bekannten „Föderationsprotokolle“ (Webfinger, Salmon, Activitystreams, Portablecontacts usw.) können in Nomad verwendet werden, aber wir sind nicht an sie gebunden und werden auch nicht an sie gebunden sein. Das Hubzilla-Projekt versucht einige ziemlich neuartige Entwicklungen im Bereich der dezentralen Kommunikation, und wenn es notwendig sein sollte, von solchen „Standardprotokollen“ abzuweichen, werden wir dies ohne Fragen oder Zögern tun. 

Um eine weltweit eindeutige ID zu erstellen, werden wir sie auf einem Whirlpool-Hash der Identitäts-URL des Ursprungsknotens und einer Pseudo-Zufallszahl basieren, was uns eine 256-Bit-ID mit einer extrem geringen Kollisionswahrscheinlichkeit liefern sollte (256 Bits entsprechen ungefähr 115 Quattuorviginitillionen oder 1,16 X 10^77 eindeutigen Zahlen). Diese wird in der Kommunikation als base64url-kodierte Zeichenfolge dargestellt. Wir werden uns jedoch nicht auf Wahrscheinlichkeiten verlassen, und die ID muss auch mit einem öffentlichen Schlüssel verbunden sein, wobei die Kryptographie mit öffentlichem Schlüssel verwendet wird, um eine Identitätsgarantie zu bieten, die nicht kopiert oder irgendwie im Whirlpool-Hash-Raum kollidiert wurde.

Durch die Verwendung der DNS als Grundlage für die ID wird ein weltweit eindeutiger Seed bereitgestellt, was nicht der Fall wäre, wenn die ID vollständig auf einer Pseudo-Zufallszahlengenerierung basieren würde.  

Wir bezeichnen die kodierte, weltweit eindeutige uid-Zeichenkette als zot_uid 

Da es mehr als einen DNS-Standort geben kann, der mit einer bestimmten zot_uid-Identität verknüpft/verbunden ist, sollten die Zustellungsprozesse an alle diese Standorte liefern, da wir nicht mit Sicherheit wissen, auf welche Hub-Instanz zu einem bestimmten Zeitpunkt zugegriffen werden kann. Wir werden jedoch einen DNS-Standort als „primär“ bezeichnen, der der bevorzugte Standort für die Anzeige von Webprofilinformationen sein wird.

Wir müssen auch die Möglichkeit bieten, den primären Standort auf einen neuen Standort zu ändern. Ein Nachschlagen von Informationen über den aktuellen primären Standort kann einen „Weiterleitungszeiger“ liefern, der uns anweist, unsere Einträge zu aktualisieren und alles an den neuen Standort zu verschieben. Es besteht auch die Möglichkeit, dass der Hauptstandort nicht mehr existiert und nicht mehr antwortet. In diesem Fall kann ein Ort, der bereits dieser zot_uid zugeordnet ist, die Kontrolle übernehmen und sich selbst als primär deklarieren. In beiden Fällen wird die primäre Bezeichnung automatisch bestätigt und verschoben. Es kann auch ein Antrag von einem primären Standort gestellt werden, der die Entfernung eines anderen Standorts verlangt. 

Um eine zot_uid einem zweiten (oder tertiären) Standort zuzuordnen, ist ein sicherer Austausch erforderlich, bei dem überprüft wird, ob der neue Standort im Besitz des privaten Schlüssels für diese zot_uid ist. Die Sicherheit des privaten Schlüssels ist daher von grundlegender Bedeutung, um ein Hijacking von Identitäten zu verhindern.

Die Kommunikation erfordert dann

- zot_uid (Zeichenfolge)
- uid_sig
- callback (aktueller Standort Nomad-Endpunkt url)
- callback_sig
- spec (int)

wird mit jeder Mitteilung übergeben. Bei spec handelt es sich um eine Revisionsnummer der geltenden Nomad-Spezifikation, damit die Kommunikation mit Hubs, die ältere und möglicherweise inkompatible Protokollspezifikationen verwenden, aufrechterhalten werden kann. Die Kommunikation wird mit Hilfe eines gespeicherten öffentlichen Schlüssels verifiziert, der beim ersten Aufbau der Verbindung zu dieser zot_uid kopiert wurde. 

Der Widerruf und Ersatz des Schlüssels muss vom primären Hub aus erfolgen. Die genaue Form hierfür wird noch ausgearbeitet, wird aber wahrscheinlich eine Benachrichtigung an alle anderen gebundenen Hubs sein, um „nach Hause zu telefonieren“ (zum primären Hub) und eine Kopie des neuen Schlüssels anzufordern. Diese Mitteilung sollte mit einem Standort- oder Hub-Schlüssel verifiziert werden, da der ursprüngliche Identitätsschlüssel kompromittiert worden sein könnte und nicht vertrauenswürdig ist.   

Um Verwirrung zu vermeiden, sollte es für jeden Hub genau eine kanonische Url geben, damit diese indiziert und eindeutig referenziert werden kann. 

Um Mehrdeutigkeit des Schemas zu vermeiden, wird dringend empfohlen, dass alle Adressen https mit einem „browser valid“-Zertifikat und einer einzigen gültigen Host-Komponente (entweder www.domain.ext oder domain.ext, aber nicht beides) sein sollten, die in der gesamten Kommunikation verwendet wird. Es können mehrere URLs lokal angegeben werden, aber es sollte nur eine einzige URL für die gesamte Nomad-Kommunikation innerhalb des Grids verwendet werden.

Testinstallationen, die nicht mit dem öffentlichen Netz verbunden sind, können Nicht-SSL verwenden, aber der gesamte Datenverkehr, der über öffentliche Netze fließt, sollte vor Session-Hijacking geschützt sein, vorzugsweise mit einem „browsererkannten“ Zertifikat.

Wenn möglich, empfiehlt das Nomadt die Verwendung von „Batching“, um den Netzwerkverkehr zwischen zwei Hubs zu minimieren. Dies bedeutet, dass Standort A„ mehrere Nachrichten an Standort B“ in einer einzigen Transaktion senden kann und auch die Zustellung identischer Nachrichten an mehrere Empfänger im selben Hub konsolidiert werden kann.

Die Nachrichten selbst können während der Übertragung verschlüsselt werden oder auch nicht, je nach der privaten Natur der Nachrichten. SSL (dringend empfohlen) bietet eine bedingungslose Verschlüsselung des Datenstroms, allerdings macht es wenig Sinn, öffentliche Mitteilungen zu verschlüsseln, die als uneingeschränkt sichtbar eingestuft wurden. Die Verschlüsselung der Datenspeicherung und die sogenannte „Ende-zu-Ende-Verschlüsselung“ liegen außerhalb des Anwendungsbereichs des Nomad. Es wird davon ausgegangen, dass die Betreiber von Knotenpunkten angemessene Sicherheitsvorkehrungen treffen, um die Sicherheit ihrer Datenspeicher zu gewährleisten, und diese sind Funktionen der Anwendungs- und Standortintegrität im Gegensatz zur Protokollintegrität. 



#### Nachrichten  

In Anbetracht der zuvor aufgeführten Einschränkungen sollte eine Nomad-Mitteilung daher ein json-Array aus einzelnen Nachrichten sein. Diese können innerhalb der gleichen Übertragung gemischt und kombiniert werden.

Jede Nachricht erfordert dann:

- Typ
- (optional) Empfängerliste

Das Fehlen einer Empfängerliste würde auf eine unverschlüsselte öffentliche Nachricht oder eine Nachricht auf Standortebene hinweisen. Die Empfängerliste würde ein Array von zot_uid mit einem individuellen Entschlüsselungsschlüssel und einer gemeinsamen iv enthalten. Der Entschlüsselungsschlüssel ist mit dem öffentlichen Schlüssel der Empfängeridentität verschlüsselt. Die iv wird mit dem privaten Schlüssel des Absenders verschlüsselt.

Alle Nachrichten sollten vom Absender digital signiert werden.    

Der Typ kann einer der folgenden sein (diese Liste ist erweiterbar):

- Post (oder Aktivität)
- Mail
- Identität
- authentifizieren

Identitätsnachrichten haben keine Empfänger und benachrichtigen den sozialen Graphen des Systems über eine Identitätsaktualisierung, bei der es sich um eine neue oder gelöschte Identität, einen neuen oder gelöschten Standort oder eine Änderung des primären Hubs handeln kann. Die Signatur für diese Nachrichten verwendet Systemschlüssel im Gegensatz zu identitätsspezifischen Schlüsseln.

Posts umfassen viele verschiedene Arten von Aktivitäten, wie z. B. Top-Level-Posts, Likes/Dislikes, Kommentare, Tagging-Aktivitäten usw. Diese Typen werden in der Nachrichtenstruktur angegeben.

Authentifizierungsnachrichten führen zu einer gegenseitigen Authentifizierung und einer Browserumleitung zu geschützten Ressourcen auf dem entfernten Hub, wie z. B. die Möglichkeit, Nachrichten von Wand zu Wand zu posten und private Fotoalben und Ereignisse einzusehen, usw.



#### Erkundung  

Eine bekannte URL wird verwendet, um einen Hub auf Nomad-Fähigkeiten und Identitätsabfragen zu untersuchen, einschließlich der Ermittlung von öffentlichen Schlüsseln, Profilstandorten, Profilfotos und dem primären Hub-Standort. 

Der Speicherort für diesen Dienst ist /.well-known/zot-info - und muss im Stammverzeichnis der gewählten Domain verfügbar sein.

Um eine Abfrage durchzuführen, wird eine POST-Anfrage mit den folgenden Parametern an den Erkennungsort gestellt:

Erforderlich:

Adresse => eine Adresse auf dem Zielsystem wie „john“

Optional:

target => die Nomad „guid“ des Beobachters für die Auswertung der Berechtigungen

target_sig => eine RSA-Signatur (base64url-kodiert) der guid

key => Der öffentliche Schlüssel, der zur Verifizierung der Signatur benötigt wird

token => eine vom anfragenden Dienst gewählte Zeichenfolge (möglicherweise zufällig). Falls vorhanden, wird ein Eintrag im entdeckten Paket mit der Bezeichnung „signed_token“ bereitgestellt, der aus der base64url_kodierten RSA-Signatur der Verkettung der Zeichenkette „token.“ und dem bereitgestellten Token unter Verwendung des privaten Schlüssels des entdeckten Kanals besteht. Dies kann mit dem bereitgestellten „key“-Eintrag verifiziert werden und bietet die Sicherheit, dass der Server im Besitz des privaten Schlüssels für die ermittelte Identität ist. Nach dem 01.01.2017 ist es **erforderlich,** dass ein Server ein signed_token bereitstellt *, wenn* ein Token in der Anfrage angegeben wurde.  

Wenn kein Ziel angegeben wird, sind die zurückgegebenen Berechtigungen generische Berechtigungen 
für unbekannte oder unauthentifizierte Beobachter

Beispiel eines Erkennungspakets für '`mike@zothub.com`'

```
    {

    "success": true,
    "signed_token": "KBJrKTq1qrctNuxF3GwVh3GAGRqmgkirlXANPcJZAeWlvSt_9TMV097slR4AYnYCBEushbVqHEJ9Rb5wHTa0HzMbfRo8cRdl2yAirvvv5d98dtwHddQgX1jB0xEypXtmIYMdPGDLvhI1RNdIBhHkkrRcNreRzoy4xD--HM6m1W0-A8PJJJ9BcNxmGPcBtLzW08wzoP9trJ3M7DQ6Gkk6j7iwVsyApw1ZBaDvabGTdc_SFV-Iegtqw3rjzT_xXWsfzMlKBy-019MYn_KS-gu23YzjvGu5tS_zDfkQb8DMUlPLz5yyxM0yOMlUDtG2qQgIJAU2O0X6T5xDdJ6mtolNyhepg845PvFDEqBQGMIH1nc47CNumeudDi8IWymEALhjG_U8KAK7JVlQTJj2EKUb0au1g6fpiBFab5mmxCMtZEX3Jreyak5GOcFFz-WpxuXJD9TdSoIvaBfBFOoJnXkg2zE4RHXeQzZ2FotmrbBG5dm8B-_6byYGoHBc08ZsWze1K96JIeRnLpBaj6ifUDcVHxZMPcGHHT27dvU2PNbgLiBjlAsxhYqkhN5qOHN8XBcg2KRjcMBaI3V0YMxlzXz5MztmZq3fcB1p-ccIoIyMPMzSj3yMB7J9CEU2LYPSTHMdPkIeDE6GaCkQKviaQQJQde346tK_YjA2k7_SOBmvPYE",
    "guid": "sebQ-IC4rmFn9d9iu17m4BXO-kHuNutWo2ySjeV2SIW1LzksUkss12xVo3m3fykYxN5HMcc7gUZVYv26asx-Pg",
    "guid_sig": "Llenlbl4zHo6-g4sa63MlQmTP5dRCrsPmXHHFmoCHG63BLq5CUZJRLS1vRrrr_MNxr7zob_Ykt_m5xPKe5H0_i4pDj-UdP8dPZqH2fqhhx00kuYL4YUMJ8gRr5eO17vsZQ3XxTcyKewtgeW0j7ytwMp6-hFVUx_Cq08MrXas429ZrjzaEwgTfxGnbgeQYQ0R5EXpHpEmoERnZx77VaEahftmdjAUx9R4YKAp13pGYadJOX5xnLfqofHQD8DyRHWeMJ4G1OfWPSOlXfRayrV_jhnFlZjMU7vOdQwHoCMoR5TFsRsHuzd-qepbvo3pzvQZRWnTNu6oPucgbf94p13QbalYRpBXKOxdTXJrGdESNhGvhtaZnpT9c1QVqC46jdfP0LOX2xrVdbvvG2JMWFv7XJUVjLSk_yjzY6or2VD4V6ztYcjpCi9d_WoNHruoxro_br1YO3KatySxJs-LQ7SOkQI60FpysfbphNyvYMkotwUFI59G08IGKTMu3-GPnV1wp7NOQD1yzJbGGEGSEEysmEP0SO9vnN45kp3MiqbffBGc1r4_YM4e7DPmqOGM94qksOcLOJk1HNESw2dQYWxWQTBXPfOJT6jW9_crGLMEOsZ3Jcss0XS9KzBUA2p_9osvvhUKuKXbNztqH0oZIWlg37FEVsDs_hUwUJpv2Ar09k4",
    "key": "-----BEGIN PUBLIC KEY-----\nMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA7QCwvuEIwCHjhjbpz3Oc\ntyei/Pz9nDksNbsc44Cm8jxYGMXsTPFXDZYCcCB5rcAhPPdZSlzaPkv4vPVcMIrw\n5cdX0tvbwa3rNTng6uFE7qkt15D3YCTkwF0Y9FVZiZ2Ko+G23QeBt9wqb9dlDN1d\nuPmu9BLYXIT/JXoBwf0vjIPFM9WBi5W/EHGaiuqw7lt0qI7zDGw77yO5yehKE4cu\n7dt3SakrXphL70LGiZh2XGoLg9Gmpz98t+gvPAUEotAJxIUqnoiTA8jlxoiQjeRK\nHlJkwMOGmRNPS33awPos0kcSxAywuBbh2X3aSqUMjcbE4cGJ++/13zoa6RUZRObC\nZnaLYJxqYBh13/N8SfH7d005hecDxWnoYXeYuuMeT3a2hV0J84ztkJX5OoxIwk7S\nWmvBq4+m66usn6LNL+p5IAcs93KbvOxxrjtQrzohBXc6+elfLVSQ1Rr9g5xbgpub\npSc+hvzbB6p0tleDRzwAy9X16NI4DYiTj4nkmVjigNo9v2VPnAle5zSam86eiYLO\nt2u9YRqysMLPKevNdj3CIvst+BaGGQONlQalRdIcq8Lin+BhuX+1TBgqyav4XD9K\nd+JHMb1aBk/rFLI9/f2S3BJ1XqpbjXz7AbYlaCwKiJ836+HS8PmLKxwVOnpLMbfH\nPYM8k83Lip4bEKIyAuf02qkCAwEAAQ==\n-----END PUBLIC KEY-----\n",
    "name": "Mike Macgirvin",
    "name_updated": "2012-12-06 04:59:13",
    "address": "mike@zothub.com",
    "photo_mimetype": "image/jpeg",
    "photo": "https://zothub.com/photo/profile/l/1",
    "photo_updated": "2012-12-06 05:06:11",
    "url": "https://zothub.com/channel/mike",
    "connections_url": "https://zothub.com/poco/mike",
    "target": "",
    "target_sig": "",
    "searchable": false,
    "permissions": {
        "view_stream": true,
        "view_profile": true,
        "view_photos": true,
        "view_contacts": true,
        "view_storage": true,
        "view_pages": true,
        "send_stream": false,
        "post_wall": false,
        "post_comments": false,
        "post_mail": false,
        "post_photos": false,
        "tag_deliver": false,
        "chat": false,
        "write_storage": false,
        "write_pages": false,
        "delegate": false
    },
    "profile": {
        "description": "Freedom Fighter",
        "birthday": "0000-05-14",
        "next_birthday": "2013-05-14 00:00:00",
        "gender": "Male",
        "marital": "It's complicated",
        "sexual": "Females",
        "locale": "",
        "region": "",
        "postcode": "",
        "country": "Australia"
    },
    "locations": [
        {
            "host": "zothub.com",
            "address": "mike@zothub.com",
            "primary": true,
            "url": "https://zothub.com",
            "url_sig": "eqkB_9Z8nduBYyyhaSQPPDN1AhSm5I4R0yfcFxPeFpuu17SYk7jKD7QzvmsyahM5Kq7vDW6VE8nx8kdFYpcNaurqw0_IKI2SWg15pGrhkZfrCnM-g6A6qbCv_gKCYqXvwpSMO8SMIO2mjQItbBrramSbWClUd2yO0ZAceq3Z_zhirCK1gNm6mGRJaDOCuuTQNb6D66TF80G8kGLklv0o8gBfxQTE12Gd0ThpUb5g6_1L3eDHcsArW_RWM2XnNPi_atGNyl9bS_eLI2TYq0fuxkEdcjjYx9Ka0-Ws-lXMGpTnynQNCaSFqy-Fe1aYF7X1JJVJIO01LX6cCs-kfSoz29ywnntj1I8ueYldLB6bUtu4t7eeo__4t2CUWd2PCZkY3PKcoOrrnm3TJP5_yVFV_VpjkcBCRj3skjoCwISPcGYrXDufJxfp6bayGKwgaCO6QoLPtqqjPGLFm-fbn8sVv3fYUDGilaR3sFNxdo9mQ3utxM291XE2Pd0jGgeUtpxZSRzBuhYeOybu9DPusID320QbgNcbEbEImO8DuGIxuVRartzEXQF4WSYRdraZzbOqCzmU0O55P836JAfrWjgxTQkXlYCic-DBk-iE75JeT72smCtZ4AOtoFWCjZAABCw42J7JELY9APixZXWriKtjy6JI0G9d3fs6r7SrXr1JMy0",
            "callback": "https://zothub.com/post",
            "sitekey": "-----BEGIN PUBLIC KEY-----\nMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA1IWXwd/BZuevq8jzNFoR\n3VkenduQH2RpR3Wy9n4+ZDpbrUKGJddUGm/zUeWEdKMVkgyllVA/xHdB7jdyKs1X\nuIet9mIdnzvhdLO/JFD5hgbNG2wpSBIUY6aSNeCFTzszqXmuSXMW5U0Ef5pCbzEA\nnhoCoGL1KAgPqyxnGKUlj7q2aDwC9IRNtAqNyFQL67oT91vOQxuMThjlDhbR/29Q\ncYR4i1RzyahgEPCnHCPkT2GbRrkAPjNZAdlnk9UesgP16o8QB3tE2j50TVrbVc/d\nYRbzC56QMPP9UgUsapNeSJBHji75Ip/E5Eg/kfJC/HEQgyCqjCGfb7XeUaeQ7lLO\nqc7CGuMP+Jqr/cE54/aSHg8boTwxkMp11Ykb+ng17fl57MHTM2RJ99qZ1KBkXezR\nuH1lyvjzeJPxEFr9rkUqc4GH74/AgfbgaFvQc8TS7ovEa5I/7Pg04m7vLSEYc6UF\nYJYxXKrzmZT2TDoKeJzaBBx5MFLhW19l68h9dQ8hJXIpTP0hJrpI+Sr6VUAEfFQC\ndIDRiFcgjz6j7T/x8anqh63/hpsyf2PMYph1+4/fxtSCWJdvf+9jCRM8F1IDfluX\n87gm+88KBNaklYpchmGIohbjivJyru41CsSLe0uinQFvA741W00w6JrcrOAX+hkS\nRQuK1dDVwGKoIY85KtTUiMcCAwEAAQ==\n-----END PUBLIC KEY-----\n"
        }
    ],
    "site": {
        "url": "https://zothub.com",
        "directory_mode": "primary",
        "directory_url": "https://zothub.com/dirsearch"
    }

    }
```

Discovery gibt ein JSON-Array mit den folgenden Komponenten zurück:

'success' => (true oder false) Operation war erfolgreich, wenn true. Andernfalls kann eine optionale 'message' vorhanden sein, die die Fehlerursache angibt.

'signed_token' => Wenn ein Token-Parameter in der Anfrage angegeben wurde, wird ihm der Text 'token.' vorangestellt und dann mit dem privaten Schlüssel des Kanals RSA-signiert und base64url-kodiert und als 'signed_token' zurückgegeben.

guid' => die guid der Adresse auf dem Zielsystem

guid_sig“ => die mit base64url kodierte RSA-Signatur der guid, signiert mit dem mit dieser guid verbundenen privaten Schlüssel.

'key' => der öffentliche Schlüssel, der mit dieser guid verknüpft ist

name“ => Name des Kanals

name_updated' => Zeitstempel im MySQL-Stil '2012-01-01 00:00:00', als der Name zuletzt geändert wurde (UTC)

address' => „webbie“ oder user@host-Adresse, die mit diesem Kanal verbunden ist

photo' => URL eines Profilfotos für diesen Kanal (idealerweise 175x175)

photo_mimetype“ => Inhaltstyp des Profilfotos

photo_updated' => Zeitstempel im MySQL-Stil, wann das Foto zuletzt aktualisiert wurde (UTC)  

'url' => Standort der Kanal-Homepage

'connections_url' => Speicherort der URL für tragbare Kontakte (erweitert für Nomad) für diesen Kanal

target' => wenn ein Berechtigungsziel angegeben wurde, wird es gespiegelt

'target_sig' => wenn ein Berechtigungsziel angegeben wurde, wird die Signatur gespiegelt.

'searchable' => (true oder false) true bedeutet, dass dieser Eintrag in einem Verzeichnis gesucht werden kann



##### Berechtigungen  

'permissions' => erweiterbares Array von Berechtigungen, die für dieses Ziel geeignet sind, Werte sind true oder false

Berechtigungen können beinhalten:

-  view_stream
-  view_profile
-  view_photos
-  view_contacts
-  view_storage
-  view_pages
-  send_stream
-  post_wall
-  post_comments
-  post_mail
-  post_photos
-  tag_deliver
-  chat
-  write_storage
-  write_pages
-  delegate

##### Profil   

'Profil' => Array mit wichtigen Profilfeldern

-  description
-  birthday YYYY-MM-DD , all fields are optional, any field (such as year) may be zero
-  next_birthday => MySQL datetime string representing the next  upcoming birthday, converted from the channel's default timezone to UTC. 
-  gender (free form)
-  marital (marital status)
-  sexual (preference)
-  locale (city)
-  region (state)
-  postcode
-  country

##### Standorte   

'locations' => Array der registrierten Standorte (DNS-Standorte), von denen aus dieser Kanal sichtbar sein kann oder gebucht werden kann

Jeder Standort ist ein Array aus

'host' => DNS-Hostname, z.B. example.com

'address' => die Webbie- oder user@host-Kennung, die mit diesem Standort verbunden ist

'primary' => (true oder false), ob dies der primäre Ort für diesen Kanal ist, an dem Dateien und Webseiten im Allgemeinen zu finden sind

url“ => Url des Stammverzeichnisses dieses DNS-Speicherorts, z. B. `https://example.com`

url_sig' => base64url kodierte RSA-Signatur der URL, signiert mit dem privaten Schlüssel des Kanals

'callback' => Nomad-Kommunikationsendpunkt auf dieser Site, normalerweise `https://example.com/post]`

sitekey' => öffentlicher Schlüssel dieser Seite/Host



##### Standort   

'site' => Array, das die Verzeichnisrolle der Site angibt, die auf diese Anfrage antwortet

url' => url dieser Site z.B. `https://example.com`

directory_mode' => eines von 'primary', 'secondary', 'normal' oder 'standalone'

directory_url' => wenn es sich um einen Verzeichnisserver oder einen Einzelplatz handelt, die URL für das Verzeichnissuchmodul