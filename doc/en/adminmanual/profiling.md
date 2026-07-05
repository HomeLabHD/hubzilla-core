## Performance profiling

To be able to identify what is causing performance issues in a hub, we need to profile the application. This can of course be done completely independent of $Projectname, but to make it easier, $Projectname provides profiling support built right into the core.

Performance profiling is still an advanced troubleshooting technique, and something you're unlikely to need unless asked by a developer, or you're proficient in debugging PHP applications yourself.

### Prerequisites

$Projectname supports the `xhprof` profiler, which is an extension that must be available and loaded in your PHP setup before profiling can be enabled. See the [PHP manual][1] for how to set up your system for profiling using xhprof.

[1]: https://www.php.net/manual/en/book.xhprof.php


### Configuring the profiler

By default the profiler will save the traces in a file called `xhgui.data.jsonl` in the root directory of your site. You can change this to any pathname you want, as long as the process serving the site had write access to it. The path name is relative to the root directory of the site.

It is also possible to have the profiler send the data directly to an existing Xhgui instance.

To configure the profiler, go to [baseurl]/admin/profiler.


### Enabling the profiler

An administrator can enable the profiler in the HQ page of one of their channels. Profiling can also be enabled by setting the `system.profiling_enabled` config uption to true or 1.

When profiling is enabled, the system will trace all requests, and store or transmit the tracing info as specified in the configuration.

Be aware that if profiling a busy site, the data will grow quickly. It is best to only enable it for short periods at the time to not overload the system.

### Disabling the profiler

An administrator can disable the profiler in the HQ page of one of their channels. It can also be disabled by setting the `system.profiling_enabled` to false or 0.


### Analyzing the profiling data

The data collected during profiling is saved in a format compatible with the XHGui web based frontend. The data can either be imported into the tool from the file specified in the configuration, or sent directly to a XHGui instance reachable over the web.

See the [XHGui readme file][2] for more information about how to set up XHGui locally or on a server.

[2]: https://github.com/perftools/xhgui
