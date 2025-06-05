### Nomad - Ein Überblick auf oberer Ebene 

Hier ist eine allgemeine Beschreibung der Funktionsweise von Nomad.

In diesem Beispiel wird „Indigo“ eine öffentliche Nachricht von seiner Website „podunk.edu“ aus versenden. „Nickordo„ ist ein Empfänger auf einer anderen Website (“example.com").

Indigo stellt seine Nachricht zuerst auf podunk.edu ein. podunk.edu schaut nach, wer die Nachricht erhalten soll und findet Nickordo. Nickordo postet normalerweise von example.com aus, also fügen wir dieses Ziel zu unserer Liste der Empfänger hinzu. Wir können auch andere Ziele für Nickordo und alle anderen, die Indigos Beiträge verfolgen, hinzufügen.

In diesem Beispiel stellen wir fest, dass wir nur einen bekannten Empfänger an einem bekannten Ort haben.

Wir senden ein Paket an example.com:



    {
      "type":"notify",
      "sender":{
        "guid":"kgVFf_1_SSbyqH-BNWjWuhAvJ2EhQBTUdw-Q1LwwssAntr8KTBgBSzNVzUm9_RwuDpxI6X8me_QQhZMf7RfjdA",
        "guid_sig":"PT9-TApzpm7QtMxC63MjtdK2nUyxNI0tUoWlOYTFGke3kNdtxSzSvDV4uzq_7SSBtlrNnVMAFx2_1FDgyKawmqVtRPmT7QSXrKOL2oPzL8Hu_nnVVTs_0YOLQJJ0GYACOOK-R5874WuXLEept5-KYg0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81-Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91-ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD-yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q",
        "url":"http:\/\/podunk.edu",
        "url_sig":"T8Bp7j5DHHhQDCFcAHXfuhUfGk2P3inPbImwaXXF1xJd3TGgluoXyyKDx6WDm07x0hqbupoAoZB1qBP3_WfvWiJVAK4N1FD77EOYttUEHZ7L43xy5PCpojJQmkppGbPJc2jnTIc_F1vvGvw5fv8gBWZvPqTdb6LWF6FLrzwesZpi7j2rsioZ3wyUkqb5TDZaNNeWQrIEYXrEnWkRI_qTSOzx0dRTsGO6SpU1fPWuOOYMZG8Nh18nay0kLpxReuHCiCdxjXRVvk5k9rkcMbDBJcBovhiSioPKv_yJxcZVBATw3z3TTE95kGi4wxCEenxwhSpvouwa5b0hT7NS4Ay70QaxoKiLb3ZjhZaUUn4igCyZM0h6fllR5I6J_sAQxiMYD0v5ouIlb0u8YVMni93j3zlqMWdDUZ4WgTI7NNbo8ug9NQDHd92TPmSE1TytPTgya3tsFMzwyq0LZ0b-g-zSXWIES__jKQ7vAtIs9EwlPxqJXEDDniZ2AJ6biXRYgE2Kd6W_nmI7w31igwQTms3ecXe5ENI3ckEPUAq__llNnND7mxp5ZrdXzd5HHU9slXwDShYcW3yDeQLEwAVomTGSFpBrCX8W77n9hF3JClkWaeS4QcZ3xUtsSS81yLrp__ifFfQqx9_Be89WVyIOoF4oydr08EkZ8zwlAsbZLG7eLXY"
      },
      "callback":"\/post",
      "version":1,
      "secret":"1eaa6613699be6ebb2adcefa5379c61a3678aa0df89025470fac871431b70467"
    }

In diesem Paket steht das Folgende:

Ich bin Indigo und hier ist der Beweis. Ich poste von podunk.edu und hier ist der Beweis. Ich habe ein Paket für Sie. Die Kontrollnummer lautet „1eaa6613....“. 

Example.com nimmt dieses Paket an und sagt: „Moment mal - ich kenne Sie nicht. Ich möchte beweisen, wer du bist.“ Example.com stellt also eine Verbindung zu podunk.edu über eine „bekannte URL“ her, die wir für diesen Zweck verwenden, und sucht nach der oben genannten „guid“. Es sollte eine Reihe von Informationen zurückgeben, darunter auch einen öffentlichen Schlüssel. Example.com verwendet diesen Schlüssel, um die Signaturen in der Nachricht zu überprüfen und festzustellen, ob es tatsächlich eine Person namens Indigo bei podunk.edu gibt. Wir müssen dies nur einmal tun. (Beachten Sie, dass Indigo von jedem Ort aus posten kann. Wir müssen nur beweisen, dass es sich um Indigo handelt und dass Indigo beweisen kann, dass er von einem anderen Standort aus postet).

Dann trennt example.com die Verbindung und weist darauf hin, dass eine Nachricht auf podunk.edu wartet. Entweder sofort oder immer dann, wenn der Drang besteht (je nachdem, wie wichtig Indigo für jemanden auf dieser Website ist), „ruft“ example.com podunk.edu an. Die Nachricht lautet in etwa so:



    {
      "type":"pickup",
      "url":"http:\/\/example.com",
      "callback_sig":"teE1_fLIqfyeCuZY4iS7sNU8jUlUuqYOYBiHLarkC99I9K-uSr8DAwVW8ZPZRK-uYdxRMuKFb6cumF_Gt9XjecCPBM8HkoXHOi_VselzJkxPwor4ZPtWYWWaFtRfcAm794LrWjdz62zdESTQd2JJIZWbrli1sUhK801BF3n0Ye6-X1MWhy9EUTVlNimOeRipcuD_srMhUcAXOEbLlrugZ8ovy2YBe6YOXkS8jj0RSFjsOduXAoVhQmNpcobSYsDvaQS3e3MvE6-oXE602zGQhuNLr7DIMt9PCdAeQo-ZM-DHlZGCkGk4O2oQFCXFzGPqLUMWDACGJfTfIWGoh_EJqT_SD5b_Yi_Wk9S1lj7vb-lmxe5JuIf7ezWzHoBT8vswnZxPYlidH2i9wapdzij9il_qqcCWWHIp7q_XkY_Zj52Z4r4gdmiqM-8y1c_1SDX7hrJFRwqL_PKFbEvyi5nMWTEzqp55Tay5Woiv19STK_H_8ufFfD9AOkYnk6rIOMsk9dn3a5tAFpDRyRndXkBWAXwiJjiND2zjue7BFu7Ty40THXcfYRh1a5XrAXcaGeYuagg-8J9tAufu9_LY3qGazFg8kRBVMOn4M8DRKSIhKj7z4MnbYL0s09gREojy4jqWO3VkaOjP2jUGzoPuUDLasudE1ehWFq0K_MTQNavgmp8",
      "callback":"http:\/\/example.com\/post",
      "secret":"1eaa6613699be6ebb2adcefa5379c61a3678aa0df89025470fac871431b70467",
      "secret_sig":"O7nB4_UJHBXi28Suwl9LBZF9hI_9KGVTgehnUlWF1oYMNRnBbVHB9lzUfAoalvp3STbU3xJbtD_S58tv6MfV7J5j2V_S1W5ex3dulmDGB8Pt_7Fe5mbEPmjQFcfv3Eg5dUjYIuDl0TDScfrHyImj7RZIWHbwd7wWVoMzzDa_o33klpYmKZCBvObCh55bRrlFkXZs_dRuOiPwkfX0C6_XES4OyOIYl45V30rdhmf-STrf4L9dKYy_axQ12RIwRcKychvVLwlUJn3bn9lgNXRRU_HTne-09OPcJbUOdcD3DkFoKOxMULBNKPHzsCau0ICYug7S0EP6LpCom_mW78s08LyVA1vYeFZjevBCiGecj57yIAQDYi6_rpWJfihYaWHRN0oqtScUR4Bdf0bQbEHxMs4zAtrOAxfyJCbi6U1pfnGgzXzB9ulOYGnVGNTF7Ey4K7FOZIBtk0ILY2JfvBUaVvVs8ttagOOHmhWhnbCvrnOFlkNdlce7zoJCSUJENUOCYmTRfwB_Jno5fAzRnrsYU3_Z-l1mzniU_OmUPz8mPEh7PwhkqAiVlyaM-q15gn7l2lAIDk9kp2X_iCme7v4V0ADN_DbpaI_0-6mPw5HLbKrCsA-sxlSMB4DO4lDCHYkauj0l25sbfroRWB_hax1O4Q0oWyOlVJLUqEC5nuUJCCE"
    } 

Was diese Nachricht aussagt, ist: Hier ist example.com, ich habe einen Beweis, und ich bin hier, um ein Paket abzuholen. Hier ist die Sendungsverfolgungsnummer, und hier ist der Beweis, dass dies die Sendungsverfolgungsnummer ist, die Sie vermutlich an example.com gesendet haben.

Gut genug. Podunk.edu überprüft die Geschichte und tatsächlich, es ist example.com, und ja, da wartet ein Paket mit dieser Kontrollnummer. Hier ist das Paket...



    {
      "success":1,
      "pickup":{
        "notify":{
          "type":"notify",
          "sender":{
            "guid":"kgVFf_1_SSbyqH-BNWjWuhAvJ2EhQBTUdw-Q1LwwssAntr8KTBgBSzNVzUm9_RwuDpxI6X8me_QQhZMf7RfjdA",
            "guid_sig":"PT9-TApzpm7QtMxC63MjtdK2nUyxNI0tUoWlOYTFGke3kNdtxSzSvDV4uzq_7SSBtlrNnVMAFx2_1FDgyKawmqVtRPmT7QSXrKOL2oPzL8Hu_nnVVTs_0YOLQJJ0GYACOOK-R5874WuXLEept5-KYg0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81-Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91-ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD-yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q",
            "url":"http:\/\/z.podunk.edu",
            "url_sig":"T8Bp7j5DHHhQDCFcAHXfuhUfGk2P3inPbImwaXXF1xJd3TGgluoXyyKDx6WDm07x0hqbupoAoZB1qBP3_WfvWiJVAK4N1FD77EOYttUEHZ7L43xy5PCpojJQmkppGbPJc2jnTIc_F1vvGvw5fv8gBWZvPqTdb6LWF6FLrzwesZpi7j2rsioZ3wyUkqb5TDZaNNeWQrIEYXrEnWkRI_qTSOzx0dRTsGO6SpU1fPWuOOYMZG8Nh18nay0kLpxReuHCiCdxjXRVvk5k9rkcMbDBJcBovhiSioPKv_yJxcZVBATw3z3TTE95kGi4wxCEenxwhSpvouwa5b0hT7NS4Ay70QaxoKiLb3ZjhZaUUn4igCyZM0h6fllR5I6J_sAQxiMYD0v5ouIlb0u8YVMni93j3zlqMWdDUZ4WgTI7NNbo8ug9NQDHd92TPmSE1TytPTgya3tsFMzwyq0LZ0b-g-zSXWIES__jKQ7vAtIs9EwlPxqJXEDDniZ2AJ6biXRYgE2Kd6W_nmI7w31igwQTms3ecXe5ENI3ckEPUAq__llNnND7mxp5ZrdXzd5HHU9slXwDShYcW3yDeQLEwAVomTGSFpBrCX8W77n9hF3JClkWaeS4QcZ3xUtsSS81yLrp__ifFfQqx9_Be89WVyIOoF4oydr08EkZ8zwlAsbZLG7eLXY"
          },
          "callback":"\/post",
          "version":1,
          "secret":"1eaa6613699be6ebb2adcefa5379c61a3678aa0df89025470fac871431b70467"
        },
        "message":{
          "message_id":"10b049ce384cbb2da9467319bc98169ab36290b8bbb403aa0c0accd9cb072e76@podunk.edu",
          "message_top":"10b049ce384cbb2da9467319bc98169ab36290b8bbb403aa0c0accd9cb072e76@podunk.edu",
          "message_parent":"10b049ce384cbb2da9467319bc98169ab36290b8bbb403aa0c0accd9cb072e76@podunk.edu",
          "created":"2012-11-20 04:04:16",
          "edited":"2012-11-20 04:04:16",
          "title":"",
          "body":"Hi Nickordo",
          "app":"",
          "verb":"post",
          "object_type":"",
          "target_type":"",
          "permalink":"",
          "location":"",
          "longlat":"",
          "owner":{
            "name":"Indigo",
            "address":"indigo@podunk.edu",
            "url":"http:\/\/podunk.edu",
            "photo":{
              "mimetype":"image\/jpeg",
              "src":"http:\/\/podunk.edu\/photo\/profile\/m\/5"
            },
            "guid":"kgVFf_1_SSbyqH-BNWjWuhAvJ2EhQBTUdw-Q1LwwssAntr8KTBgBSzNVzUm9_RwuDpxI6X8me_QQhZMf7RfjdA",
            "guid_sig":"PT9-TApzpm7QtMxC63MjtdK2nUyxNI0tUoWlOYTFGke3kNdtxSzSvDV4uzq_7SSBtlrNnVMAFx2_1FDgyKawmqVtRPmT7QSXrKOL2oPzL8Hu_nnVVTs_0YOLQJJ0GYACOOK-R5874WuXLEept5-KYg0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81-Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91-ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD-yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q"
          },
          "author":{
            "name":"Indigo",
            "address":"indigo@podunk.edu",
            "url":"http:\/\/podunk.edu",
            "photo":{
              "mimetype":"image\/jpeg",
              "src":"http:\/\/podunk.edu\/photo\/profile\/m\/5"
            },
            "guid":"kgVFf_1_SSbyqH-BNWjWuhAvJ2EhQBTUdw-Q1LwwssAntr8KTBgBSzNVzUm9_RwuDpxI6X8me_QQhZMf7RfjdA",
            "guid_sig":"PT9-TApzpm7QtMxC63MjtdK2nUyxNI0tUoWlOYTFGke3kNdtxSzSvDV4uzq_7SSBtlrNnVMAFx2_1FDgyKawmqVtRPmT7QSXrKOL2oPzL8Hu_nnVVTs_0YOLQJJ0GYACOOK-R5874WuXLEept5-KYg0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81-Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91-ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD-yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q"
          }
        }
      }
    }



Und das ist das Paket (die ursprüngliche Nachricht). Example.com wandelt diese in eine Form um, die von Nickordo eingesehen werden kann, und benachrichtigt Nickordo, dass es eine neue Nachricht gibt. Podunk.edu **könnte** feststellen, dass noch andere Pakete auf example.com warten. Wenn dies der Fall ist, kann es auch alle anderen wartenden Pakete zu diesem Zeitpunkt versenden. Jedes dieser Pakete ist mit der ursprünglichen Sendungsnummer versehen.