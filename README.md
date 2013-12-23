Jenkins Status Board
====================

An external status board for Jenkins Build statuses that can be used on dashboard apps that don't allow you to sign into Jenkins.

Useful for development shop TVs that display status widgets. index.php auto refreshes an iframe to avoid full widget refreshing.

Successful builds are green, cancelled builds are gray, failed builds are red.

See example.html for an example that includes a failure.

Usage
=====

Set your user and url values at the top of status.php.

The script queries Jenkins for all jobs, then gets the status of each one. Jobs are abbreviated by splitting on dashes and using the first letter of each word. Ex: project-one-prod => POP. This can be modified in the foreach loop around line 83 of status.php.

Put index.php and status.php into a /status folder on your server, and point your widget to http://yourhost.com/path/to/status. It will auto-refresh inside an iframe every 5 minutes by default.
