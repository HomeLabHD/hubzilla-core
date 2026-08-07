
Dziękujemy za zarejestrowanie się na serwisie {{$sitename}}.

Szczegóły Twojego logowania są następujące:

Lokalizacja serwisu:	{{$siteurl}}
Nazwa logowania:	{{$email}}

Zaloguj się za pomocą hasła wybranego podczas rejestracji.

Musimy zweryfikować Twój adres e-mail, aby zapewnić Ci pełny dostęp.

Twój kod weryfikacyjny, to:

{{$hash}}

{{if $timeframe}}
This token is valid from {{$timeframe.0}} UTC until {{$timeframe.1}} UTC


{{/if}}
Jeśli zarejestrowałeś to konto, wprowadź kod weryfikacyjny do żądania lub odwiedź
poniższy link:

{{$siteurl}}/regate/{{$mail}}

Aby odrzucić rejestrację i usunąć konto, odwiedź:

{{$siteurl}}/regate/{{$mail}}{{if $ko}}/{{$ko}}{{/if}}


Dziękjemy.


--
Warunki świadczenia usług:
{{$siteurl}}/help/TermsOfService

