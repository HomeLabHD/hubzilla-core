### Administration

#### Website administration

The website is usually managed via the administration page, which is located under /admin on your website. To access this page, you must have administration rights for the server. Administration rights are granted to the first account that registers on your website, **provided that** the email address of this account exactly matches the email address that you specified as the administrator's email address during setup.
There are several ways in which this can fail and the system can be left without an administrator account, for example if the first account created has a different email address to the administrator email address specified during setup.

![adm01](/help/en/adminmanual/pic/adm01.png)

For security reasons, there is no web page or interface on the system that gives you administrator rights. If you need to correct a situation where a system does not have an administrator account, this **must** be done by editing the account table in the database. There is no other option. To do this, you must find the entry in the account table that belongs to the desired administrator and set ‘account_roles’ for this entry to 4096. You can then call up the administrator page via the profile menu of your system or directly via /admin.
A hub can have several administrators and the number of administrators is not limited. Repeat the above procedure for each account that you want to give administration rights.
