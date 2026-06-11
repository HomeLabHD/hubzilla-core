
Tack för att du har skapat ett konto på {{$sitename}}.

Dina inloggningsuppgifter är:

Serveradress:	{{$siteurl}}
Användarnamn:	{{$email}}

Logga in med lösenordet som du valde vid registreringen.

Your verification token is

{{$hash}}

{{if $timeframe}}
This token is valid from {{$timeframe.0}} UTC until {{$timeframe.1}} UTC


{{/if}}
Vi behöver bekräfta din e-postadress för att ge dig full åtkomst.

Om du registrerade det här kontot, följ den här länken:

{{$siteurl}}/regate/{{$mail}}


För att avbryta registreringen och ta bort kontot, gå till:


{{$siteurl}}/regate/{{$mail}}{{if $ko}}/{{$ko}}{{/if}}


Tack.


--
Terms Of Service:
{{$siteurl}}/help/TermsOfService
