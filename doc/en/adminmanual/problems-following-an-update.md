### Problems Following An Update

A good 90% of all bugs encountered immediately after updating the code to the latest version are simple cache errors of one sort or another.  If you update and find something very obvious is broken - like your matrix page doesn't load, notifications are missing, or comment boxes are missing - the chances are it's not a bug at all.  Breaking basic functionality is the kind of thing developers tend to notice.

If this happens to you, there are a few simple steps to take before resorting to the support forums:

**Browser Cache**

Symptoms:  Menus do not expand, ACL selector does not open, progress indicator does not display (or loops forever), Matrix and channel pages do not load.

Force reload the page.  Shift reload, or ctrl+f5.  Occasionally, but very, very rarely, you will also need to clear the session data - which is achieved by restarting the browser.

**FastCGI**

Symptoms: Incorrect variables.  The basic UI mostly works, but displays incorrect content or is missing content entirely.  

If you're using php-fpm, this problem is usually resolved with 
```
service php-fpm restart
```


**Smarty Cache**

Symptoms:  

1) White Screen Of Death.  This is most prevalent on the settings and admin pages.

2) Missing icons, tabs, menus or features.

We use the Smarty3 template engine to generate pages.  These templates are compiled before they are displayed.  Occasionally, a new or modified template will fail to overwrite the old compiled version.  To clear the Smarty cache, delete all the files in store/[data]/smarty3/compiled **but do not delete the directory itself**.  Templates will then be recompiled on their next access.

**Theme Issues**

There are many themes for Hubzilla.  Only Redbasic is officialy supported by the core developers.  This applies *even if a core developer happens to support an additional theme*.  This means new features are only guaranteed to work in Redbasic.

Redbasic uses a few javascript libraries that are done differently, or entirely absent in other themes.  This means new features may only work properly in Redbasic.  Before reporting an issue, therefore, you should switch to Redbasic to see if it exists there.  If the issue goes away, this is not a bug - it's a theme that isn't up to date.

Should you report an issue with the theme developers then?  No.  Theme developers use their themes.  Chances are, they know.  Give them two or three days to catch up and *then* report the issue if it's still not fixed.  There are two workarounds for this situation.  Firstly, you can temporarily use Redbasic.  Secondly, most themes are open source too - open a pull request and make yourself a friend.
