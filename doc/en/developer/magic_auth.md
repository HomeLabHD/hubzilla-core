### Magic Auth 

The so-called ‘magic auth’ takes place via a special exchange. On the remote computer, a redirection is made to the Nomad endpoint with special GET parameters.

Endpoint: [https://example.com/post/name](https://example.com/post/name?f=&zid=pepecyb@hub.hubzilla.hu)

where ‘name’ is the left-hand side of the channel webbie, for example ‘mike’ if the webbie is [‘mike@zothub.com’](mailto:mike@zothub.com).

In addition, four parameters are passed:

- auth => the webbie of the person requesting access
- dest => the desired destination URL (urlencoded)
- sec => a random string, which is also stored locally for use during the verification phase.
- version => the Zot revision

When this packet is received, a Nomad message is sent to the auth identity:

```
    {
      ‘type’: ‘auth_check’,
      ‘sender’:{
        ‘guid’: ‘kgVFf_1_SSbyqH-BNWjWuhAvJ2EhQBTUdw-Q1LwwssAntr8KTBgBSzNVzUm9_RwuDpxI6X8me_QQhZMf7RfjdA’,
        ‘guid_sig’: "PT9-TApzpm7QtMxC63MjtdK2nUyxNI0tUoWlOYTFGke3kNdtxSzSvDV4uzq_7SSBtlrNnVMAFx2_1FDgyKawmqVtRPmT7QSXrKOL2oPzL8Hu_nnVVTs_0YOLQJJ0GYACOOK- R5874WuXLEept5-KYg0uShifsvhHnxnPIlDM9lWuZ1hSJTrk3NN9Ds6AKpyNRqf3DUdz81- Xvs8I2kj6y5vfFtm-FPKAqu77XP05r74vGaWbqb1r8zpWC7zxXakVVOHHC4plG6rLINjQzvdSFKCQb5R_xtGsPPfvuE24bv4fvN4ZG2ILvb6X4Dly37WW_HXBqBnUs24mngoTxFaPgNmz1nDQNYQu91- ekX4-BNaovjDx4tP379qIG3-NygHTjFoOMDVUvs-pOPi1kfaoMjmYF2mdZAmVYS2nNLWxbeUymkHXF8lT_iVsJSzyaRFJS1Iqn7zbvwH1iUBjD_pB9EmtNmnUraKrCU9eHES27xTwD- yaaH_GHNc1XwXNbhWJaPFAm35U8ki1Le4WbUVRluFx0qwVqlEF3ieGO84PMidrp51FPm83B_oGt80xpvf6P8Ht5WvVpytjMU8UG7-js8hAzWQeYiK05YTXk-78xg0AO6NoNe_RSRk05zYpF6KlA2yQ_My79rZBv9GFt4kUfIxNjd9OiV1wXdidO7Iaq_Q’,
        ‘url’: ‘http:\/\/podunk.edu’,
        ‘url_sig’: "T8Bp7j5DHHhQDCFcAHXfuhUfGk2P3inPbImwaXXF1xJd3TGgluoXyyKDx6WDm07x0hqbupoAoZB1qBP3_WfvWiJVAK4N1FD77EOYttUEHZ7L43xy5PCpojJQmkppGbPJc2jnTIc_F1vvGvw5fv8gBWZvPqTdb6LWF6FLrzwesZpi7j2rsioZ3wyUkqb5TDZaNNeWQrIEYXrEnWkRI_qTSOzx0dRTsGO6SpU1fPWuOOYMZG8Nh18nay0kLpxReuHCiCdxjXRVvk5k9rkcMbDBJcBovhiSioPKv_yJxcZVBATw3z3TTE95kGi4wxCEenxwhSpvouwa5b0hT7NS4Ay70QaxoKiLb3ZjhZaUUn4igCyZM0h6fllR5I6J_sAQxiMYD0v5ouIlb0u8YVMni93j3zlqMWdDUZ4WgTI7NNbo8ug9NQDHd92TPmSE1TytPTgya3tsFMzwyq0LZ0b- g- zSXWIES__jKQ7vAtIs9EwlPxqJXEDDniZ2AJ6biXRYgE2Kd6W_nmI7w31igwQTms3ecXe5ENI3ckEPUAq__llNnND7mxp5ZrdXzd5HHU9slXwDShYcW3yDeQLEwAVomTGSFpBrCX8W77n9hF3JClkWaeS4QcZ3xUtsSS81yLrp__ifFfQqx9_Be89WVyIOoF4oydr08EkZ8zwlAsbZLG7eLXY"
      },
      ‘recipients’:{
        {
        ‘guid’: ‘ZHSqb3yGar3TYV_o9S-JkD-6o_V4DhUcxtv0VeyX8Kj_ENHPI_M3SyAUucU835-mIayGMmTpqJz3ujPkcavVhA’,
        ‘guid_sig’: "JsAAXigNghTkkbq8beGMJjj9LBKZn28hZ-pHSsoQuwYWvBJ2lSnfc4r9l--WgO6sucH-SR6RiBo78eWn1cZrh_cRMu3x3LLx4y-tjixg- oOOgtZakkBg4vmOhkKPkci0mFtzvUrpY4YHySqsWTuPwRx_vOlIYIGEY5bRXpnkNCoC8J4EJnRucDrgSARJvA8QQeQQL0H4mWEdGL7wlsZp_2VTC6nEMQ48Piu6Czu5ThvLggGPDbr7PEMUD2cZ0jE4SeaC040REYASq8IdXIEDMm6btSlGPuskNh3cD0AGzH2dMciFtWSjmMVuxBU59U1I6gHwcxYEV6BubWt_jQSfmA3EBiPhKLyu02cBMMiOvYIdJ3xmpGoMY1Cn__vhHnx_vEofFOIErb6nRzbD- pY49C28AOdBA5ffzLW3ss99d0A-6GxZmjsyYhgJu4tFUAa7JUl84tMbq28Tib0HW6qYo6QWw8K1HffxcTpHtwSL5Ifx0PAoGMJsGDZDD1y_r9a4vH5pjqmGrjL3RXJJUy- m4eLV5r7xMWXsxjqu3D8r04_dcw4hwwexpMT1Nwf8CTB0TKb8ElgeOpDFjYVgrqMYWP0XdhatcFtAJI7gsS-JtzsIwON9Kij66-VAkqy_z1IXI0ziyqV1yapSVYoUV1vMScRZ_nMqwiB5rEDx-XLfzko"
        }
      }
      ‘callback’:‘\/post’,
      ‘version’:1,
      ‘secret’:‘1eaa6613699be6ebb2adcefa5379c61a3678aa0df89025470fac871431b70467’,
      ‘secret_sig’: "eKV968b1sDkOVdSMi0tLRtOhQw4otA8yFKaVg6cA4I46_zlAQGbFptS-ODiZlSAqR7RiiZQv4E2uXCKar52dHo0vvNKbpOp_ezWYcwKRu1shvAlYyytsflH5acnDWL-FKOOgz5zqLLZ6cKXFMoR1VJGG_Od- DKjSwajyV9uVzTry58Hz_w0W2pjxwQ-Xv11rab5R2O4kKSW77YzPG2R5E6Q7HN38FrLtyWD_ai3K9wlsFOpwdYC064dk66X7BZtcIbKtM6zKwMywcfJzvS5_0U5yc5GGbIY_lY6SViSfx9shOKyxkEKHfS29Ynk9ATYYGnwO-jnlMqkJC7t149H- sI9hYWMkLuCzaeLP56k2B2B2TmtnYvE_vHNQjzVhTwuHCIRVr-p6nplQn_P3SkOpYqPi3k_tnnOLa2d3Wtga8ClEY90oLWFJC3j2UkBf_VEOBNcg-t5XO3T-j9O4Sbk96k1Qoalc-QlznbGx4bOVsGkRBBMiH4YUqiiWB_OkFHtdqv7dqGeC- u-B4u9IxzYst46vvmyA3O-Q4APSZ1RY8ITUH0jLTbh6EAV7Oki8pIbOg0t56p-8RlanOZqmFvR-grVSc7Ak1ZcD8NACmvidUpa1B7WEvRcOeffx9lype0bt5XenDnMyx6szevwxZIiM8qGM2lsSk4fu8HI9cW0mLywzZT0"
    }
```

auth_check messages MUST be encrypted. This message is sent to the originating site, which checks whether the ‘secret’ matches the ‘sec’ it originally transmitted. It also checks secret_sig, which is signed with the private key of the destination channel and encoded with base64url. If everything is OK, a json packet is returned:

```
    { 
      ‘success’:1, 
      ‘confirm’: "q0Ysovd1uQRsur2xG9Tg6bC23ynzw0191SkVd7CJcYoaePy6e_v0vnmPg2xBUtIaHpx_aSuhgAkd3aVjPeaVBmts6aakT6a_yAEy7l2rBydntu2tvrHhoVqRNOmw0Q1tI6hwobk1BgK9Pm0lwOeAo8Q98BqIJxf47yO9pATa0wktOg6a7LMogC2zkkhwOV5oEqjJfeHeo27TiHr1e2WaphfCusjmk27V_FAYTzw05HvW4SPCx55EeeTJYIwDfQwjLfP4aKV- I8HQCINt-2yxJvzH7Izy9AW- 7rYU0Il_gW5hrhIS5MTM12GBXLVs2Ij1CCLXIs4cO0x6e8KEIKwIjf7iAu60JPmnb_fx4QgBlF2HLw9vXMwZokor8yktESoGl1nvf5VV5GHWSIKAur3KPS2Tb0ekNh-tIk9u-xob4d9eIf6tge_d3aq1LcAtrDBDLk8AD0bho5zrVuTmZ9k- lBVPr_DRHSV_dlpu088j3ThaBsuV1olHK3vLFRhYCDIO0CqqK5IuhqtRNnRaqhlNN6fQUHpXk2SwHiJ2W36RCYMTnno6ezFk_tN-RA2ly- FomNZoC5FPA9gFwoJR7ZmVFDmUeK3bW-zYTA5vu15lpBPnt7Up_5rZKkr0WQVbhWJmylqOuwuNWbn3SrMQ8rYFZ23Tv300cOfKVgRBaePWQb4" 
    }
```

‘confirm’ in this case is the base64url-encoded RSA signature of the concatenation of “secret” with the base64url-encoded whirlpool hash of the source guid and guid_sig; signed with the private key of the source channel. This prevents a manin-the-middle from inserting a fraudulent success packet. Upon receipt and successful verification of this packet, the destination page is redirected to the original destination URL and displays a successful remote login.