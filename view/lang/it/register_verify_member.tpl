
Grazie per esserti registrato su {{$sitename}}.

I tuoi dati di accesso sono i seguenti:

Sito:	{{$siteurl}}
Nome utente:	{{$email}}

Accedi con la password che hai scelto al momento della registrazione.

Abbiamo bisogno di verificare il tuo indirizzo e-mail per concederti l'accesso completo.

Il tuo token di verifica è

{{$hash}}

{{if $timeframe}}
Questo token è valido dalle {{$timeframe.0}} UTC alle {{$timeframe.1}} UTC.


{{/if}}
Se hai registrato questo account, inserisci il codice di convalida quando richiesto o visita il seguente link:

{{$siteurl}}/regate/{{$mail}}


Per rifiutare la richiesta e rimuovere l'account, visita:

{{$siteurl}}/regate/{{$mail}}{{if $ko}}/{{$ko}}{{/if}}


Grazie!


--
Termini di servizio:
{{$siteurl}}/help/TermsOfService
