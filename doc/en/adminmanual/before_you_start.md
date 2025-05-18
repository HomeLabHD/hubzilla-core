### Before you start 

Select a domain name or a subdomain name for your server.

The software can only be installed in the root directory of a domain or subdomain and cannot be installed via alternative TCP ports. These restrictions may be relaxed in the future, but they are inconvenient, so we STRONGLY recommend that you continue to adhere to them.

Decide if you want to use SSL and obtain an SSL certificate before installing the software. You SHOULD use SSL. If you use SSL, you MUST use a ‘browser valid’ certificate. You CANNOT use self-signed certificates!
Please test your certificate before installation. You can find a web tool for testing your certificate at [‘http://www.digicert.com/help/'](http://www.digicert.com/help/). When you visit your website for the first time, please use the SSL URL (‘https://’) if SSL is available. This will avoid problems later on. The installation routine does not allow you to use a non-browser valid certificate.

This restriction is made because public posts by you may contain links to images in your own hub. Other members viewing your stream on other hubs will receive warnings if your certificate is not trusted by their web browser. This will confuse many people as it is a decentralised network and they will receive the warning about your hub while viewing their own hub and may think their own hub has a problem. These warnings are very technical and scary for some people, many of whom don't know how to proceed other than to follow the browser's advice. This is disruptive to the community. That said, we recognise the problems associated with the current certificate infrastructure and agree that there are many issues, but that doesn't change the requirement.

Free ‘browser valid’ certificates are available from providers such as ZeroSSL, LetsEncrypt and a few others.
If you are NOT using SSL, there may be a delay of up to a minute on the first install script - while we check the SSL port to see if anything is responding there. When communicating with new sites, Hubzilla will always try to connect via the SSL port first before falling back to a less secure connection. If you are not using SSL, your web server does not HAVE to listen on port 443 at all.

If you are using LetsEncrypt to provide certificates and create a file under .well-known/acme-challenge so that LetsEncrypt can verify your domain ownership, please remove the .well-known directory or rename it once the certificate has been generated. The software provides its own handler for ‘.well-known’ services during installation, and an existing directory in this location may prevent some of these services from working correctly. This should not be a problem with Apache, but can be a problem with nginx or other web server platforms.