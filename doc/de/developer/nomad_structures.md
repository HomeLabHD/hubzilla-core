### Nomad-Strukturen

#### Nomad-Signaturen 

Alle signierten Daten in Nomad werden durch eine RSA-Signaturoperation mit dem privaten Schlüssel des Initiators erzeugt. Das binäre Ergebnis wird dann für den Transport mit base64url kodiert.

#### Nomad-Verschlüsselung 

Die Verschlüsselung erfolgt derzeit mit AES256CTR. Weitere Algorithmen KÖNNEN unterstützt werden. Ein 32-Oktett-Schlüssel und ein 16-Oktett-Initialisierungsvektor werden nach dem Zufallsprinzip erzeugt. Die gewünschten Daten werden dann mit diesen generierten Zeichenketten verschlüsselt und das Ergebnis base64url-kodiert. Dann wird ein Array erstellt:

- data 

  Die base64url-kodierten verschlüsselten Daten  

- alg 

  Der gewählte Algorithmus, in diesem Fall die Zeichenkette „aes256ctr“.  

- key 

  Der zufällig erzeugte Schlüssel, RSA-verschlüsselt mit dem öffentlichen Schlüssel des Empfängers, und das Ergebnis base64url-kodiert  

- iv 

  Der zufällig erzeugte Initialisierungsvektor, RSA-verschlüsselt mit dem öffentlichen Schlüssel des Empfängers, und das Ergebnis base64url-kodiert   

#### Grundlegendes Nomad-Paket 

Wird verwendet, um einen Dialog mit einer anderen Nomad-Site einzuleiten. Dieses Paket KANN verschlüsselt sein. Das Vorhandensein eines Array-Elements 'iv' zeigt an, dass eine Verschlüsselung vorgenommen wurde. Beim Senden eines 'auth_check'-Pakets MUSS dieses Paket verschlüsselt werden, wobei der öffentliche Schlüssel der Zielsite (der Site-Schlüssel, im Gegensatz zu einem Absenderschlüssel) verwendet wird.

```
    {
      "type":"notify",
      "sender":{
        "guid":"kgVFf_1_SSbyqH-BNWjWuhAvJ2EhQBTUdw-Q1LwwssAntr8KTBgBSzNVzUm9_RwuDpxI6X8me_QQhZMf7RfjdA",
        "guid_sig":"PT9-TApzpm7QtMxC63MjtdK2nUyxNI0tUoWlOYTFGke3kNdtxSzSvDV4uzq_7SSBtlrNnVMAFx2_1FDgyKawmqVtRPmT7QSXrKOL2oPzL8Hu_nnVVTs_0YOLQJJ0GYACOOK-R5874WuXLEept5-KYg0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81-Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91-ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD-yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q",
        "url":"http:\/\/podunk.edu",
        "url_sig":"T8Bp7j5DHHhQDCFcAHXfuhUfGk2P3inPbImwaXXF1xJd3TGgluoXyyKDx6WDm07x0hqbupoAoZB1qBP3_WfvWiJVAK4N1FD77EOYttUEHZ7L43xy5PCpojJQmkppGbPJc2jnTIc_F1vvGvw5fv8gBWZvPqTdb6LWF6FLrzwesZpi7j2rsioZ3wyUkqb5TDZaNNeWQrIEYXrEnWkRI_qTSOzx0dRTsGO6SpU1fPWuOOYMZG8Nh18nay0kLpxReuHCiCdxjXRVvk5k9rkcMbDBJcBovhiSioPKv_yJxcZVBATw3z3TTE95kGi4wxCEenxwhSpvouwa5b0hT7NS4Ay70QaxoKiLb3ZjhZaUUn4igCyZM0h6fllR5I6J_sAQxiMYD0v5ouIlb0u8YVMni93j3zlqMWdDUZ4WgTI7NNbo8ug9NQDHd92TPmSE1TytPTgya3tsFMzwyq0LZ0b-g-zSXWIES__jKQ7vAtIs9EwlPxqJXEDDniZ2AJ6biXRYgE2Kd6W_nmI7w31igwQTms3ecXe5ENI3ckEPUAq__llNnND7mxp5ZrdXzd5HHU9slXwDShYcW3yDeQLEwAVomTGSFpBrCX8W77n9hF3JClkWaeS4QcZ3xUtsSS81yLrp__ifFfQqx9_Be89WVyIOoF4oydr08EkZ8zwlAsbZLG7eLXY"
        "sitekey":"-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAxTeIwXZWrw/S+Ju6gewh
LgkKnNNe2uCUqCqMZoYgJar3T5sHCDhvXc4dDCbDkxVIaA/+V1mURtBV60a3IGjn
OAO0W0XGGLe2ED7G5o9U8T9mVGq8Mauv0v1oQ5wIR1gEAhBavkQ2OUGuF/YKn2nj
HlKsv9HzUAHpcDMUe3Uklc2RhQbMcnJxEgkyjCkDyrTtCZzISkTAocHvpCG1KSog
njUZdiz9UWxvM4rCFkCJvQU4RwRZJb7GA9ul+9JrF7NvUQTx8csRP2weBk1E9yyj
wbe187E0eVj9RXX2Mx3mYhgrTdodxLOVMSXZLg1/SMpeFFl7QBhuM0SiOPg8a7Et
e2iNA/RD4WiUFqCDfafasRa1TOozOm7LA+08lkAh5PeQPJsJbrX0wVVft++Y+5/z
BvcUOP73vcbz7j5hJ7HLsbQtye/UUCfODBFybuDqRM84Aet8rjZohX7vukXdMD4I
2HuB7pjR4uIfyYr0J63ANkvbsn8LR+RnirmHrK5H/OgxxjXcfYbGEQgFxvxhF6lA
FpMu6Do4dx3CIb6pRmZ8bjSImXexJ0BSo9n3gtrz0XYLecsYFlQ9+QQjm83qxyLb
M23in0xqMVsyQvzjNkpImrO/QdbEFRIIMee83IHq+adbyjQR49Z2hNEIZhkLPc3U
2cJJ2HkzkOoF2K37qwIzk68CAwEAAQ==
-----END PUBLIC KEY-----
"
      },
      "recipients":{
         {
            "guid":"lql-1VnxtiO4-WF0h72wLX1Fu8szzHDOXgQaTbELwXW77k8AKFfh-hYr70vqMrc3SSvWN-Flrc5HFhRTWB7ICw",
            "guid_sig":"PafvEL0VpKfxATxlCqDjfOeSIMdmpr3iU7X-Sysa1h5LzDpjSXsjO37tYZL-accb1M5itLlfnW5epkTa5I4flsW21zSY1A2jCuBQUTLLGV7rNyyBy7lgqJUFvAMRx0TfXzP9lcaPqlM9T1tA6jfWOsOmkdzwofGeXBnsjGfjsO2xdGYe6vwjOU0DSavukvzDMnOayB9DekpvDnaNBTxeGLM45Skzr7ZEMcNF7TeXMbnvpfLaALYEKeQs9bGH-UgAG8fBWgzVAzeBfx_XSR1rdixjyiZGP0kq0h35SlmMPcEjliodOBFwMXqpXFB7Ibp4F6o6te2p2ErViJccQVG8VNKB6SbKNXY6bhP5zVcVsJ-vR-p4xXoYJJvzTN7yTDsGAXHOLF4ZrXbo5yi5gFAlIrTLAF2EdWQwxSGyLRWKxG8PrDkzEzX6cJJ0VRcLh5z6OI5QqQNdeghPZbshMFMJSc_ApCPi9_hI4ZfctCIOi3T6bdgTNKryLm5fhy_eqjwLAZTGP-aUBgLZpb1mf2UojBn6Ey9cCyq-0T2RWyk-FcIcbV4qJ-p_8oODqw13Qs5FYkjLr1bGBq82SuolkYrXEwQClxnrfKa4KYc2_eHAXPL01iS9zVnI1ySOCNJshB97Odpooc4wk7Nb2Fo-Q6THU9zuu0uK_-JbK7IIl6go2qA"
         },
      },
      "callback":"\/post",
      "version":"1.2",
      "encryption":{
        "aes256ctr"
      },
      "secret":"1eaa6613699be6ebb2adcefa5379c61a3678aa0df89025470fac871431b70467",
      "secret_sig":"0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81-Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91-ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD-yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q"
    }
```

type

Der Nachrichtentyp: **notify, purge, refresh, force_refresh, auth_check, ping** oder **pickup**. Der Inhalt der Pakete variiert je nach Nachrichtentyp. Hier wird das **notify-Paket** beschrieben. 



callback

Eine Zeichenkette, die an die URL angehängt wird und den Nomad-Kommunikationsendpunkt auf diesem System identifiziert. In der Regel ist dies die Zeichenfolge „/post“.



version

Die Kennung des Nomad-Protokolls, damit künftige Protokollrevisionen nebeneinander bestehen können. 



encryption

Array der unterstützten Verschlüsselungsalgorithmen, geordnet nach abnehmender Präferenz. Wenn keine kompatiblen Verschlüsselungsmethoden angegeben sind, MÜSSEN Anwendungen „aes256cbc“ verwenden.



secret

Eine 64-stellige Zeichenkette, die von der sendenden Seite zufällig generiert wird.



secret_sig

Die RSA-Signatur des Geheimnisses, signiert mit dem privaten Schlüssel des Absenders. 



sender

Ein Array aus vier Komponenten, die eine tragbare Identität liefern. Wir können die angegebene URL kontaktieren und ein Nomad-Infopaket herunterladen, um den öffentlichen Schlüssel des Absenders zu erhalten, und diesen zur Überprüfung der Absender-Guid und der Signaturen der Entsende-URL verwenden.

- guid 

  In der Regel eine 64 Zeichen lange base64url-kodierte Zeichenfolge. Sie wird erzeugt, wenn eine Identität erstellt wird, und es wird versucht, dass sie eindeutig ist; dies ist jedoch nicht erforderlich.  

- guid_sig 

  Die RSA-Signatur der guid, signiert mit dem privaten Schlüssel des Absenders und base64url-kodiert.  

- url 

  Die Basis-URL des Ortes, von dem dieser Beitrag stammt.  

- url_sig 

  Die RSA-Signatur der url, signiert mit dem privaten Schlüssel des Absenders und base64url kodiert.  

- sitekey 

  Der öffentliche Schlüssel der in der Url angegebenen Website  

recipients

Wird nur für private Nachrichten verwendet. Ein Array von Umschlag-Empfängern. Jeder Empfänger wird durch ein Array aus guid und guid_sig dargestellt. Wenn Empfänger angegeben sind, wird das gesamte Paket auch mit einem ausgehandelten kryptografischen Algorithmus oder 'aes256cbc' gekapselt, wenn keiner ausgehandelt werden konnte. 

- guid 

  Die guid eines privaten Empfängers.  

- guid_sig 

  Die RSA-Signatur der guid, signiert mit dem privaten Schlüssel des Empfängers und base64url-kodiert