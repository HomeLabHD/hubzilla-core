
Gracias por registrarse en {{$sitename}}.

Los detalles del inicio de sesión son los siguientes:

Localización del sitio:⇥{{$siteurl}}
Nombre de usuario:⇥{{$email}}

Inicie la sesión con la contraseña que eligió durante el registro.

Necesitamos verificar su correo electrónico para poder darle pleno acceso.

Su código de validación es

{{$hash}}

{{if $timeframe}}
This token is valid from {{$timeframe.0}} UTC until {{$timeframe.1}} UTC


{{/if}}

Si ha registrado esta cuenta, introduzca el código de validación cuando se le solicite o visite el siguiente enlace:

{{$siteurl}}/regate/{{$mail}}


Para rechazar la petición y eliminar la cuenta , siga:

{{$siteurl}}/regate/{{$mail}}{{if $ko}}/{{$ko}}{{/if}}


Gracias.


--
Términos del servicio
{{$siteurl}}/help/TermsOfService
