
Vielen Dank für Ihre Registrierung bei {{$sitename}}.

Ihre Anmeldedaten lauten wie folgt:

Adresse der Seite:	{{$siteurl}}
Login-Name:	{{$email}}

Melden Sie sich mit dem Passwort an, das Sie bei der Registrierung gewählt
haben.

Wir müssen Ihre E-Mail-Adresse verifizieren, um Ihnen vollen Zugriff zu
gewähren.

Ihr Bestätigungscode lautet

{{$hash}}

{{if $timeframe}}
Dieser Code ist von {{$timeframe.0}} UTC bis {{$timeframe.1}} UTC gültig.


{{/if}}
Wenn Sie dieses Konto registriert haben, geben Sie bitte den Bestätigungscode
ein, wenn Sie dazu aufgefordert werden, oder klicken Sie auf den folgenden
Link:

{{$siteurl}}/regate/{{$mail}}


Um den Antrag abzulehnen und das Konto zu löschen, besuchen Sie bitte:

{{$siteurl}}/regate/{{$mail}}{{if $ko}}/{{$ko}}{{/if}}


Vielen Dank!


--
Nutzungsbedingungen:
{{$siteurl}}/help/TermsOfService
