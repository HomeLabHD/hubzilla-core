### Channel directory

#### Keywords  

There is a ‘keyword cloud’ with keywords that can be shown on the channel directory page. If you want to hide these keywords, which are obtained from the directory server, you can use the *configuration tool*:

```
util/config system disable_directory_keywords 1
```

If your hub is in standalone mode because you do not want to connect to the global network, you can instead ensure that the *directory_server* system option is empty:

```
util/config system directory_server ‘’
```