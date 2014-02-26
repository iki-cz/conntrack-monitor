conntrack-monitor
=================

App for monitorring conntrack connections from conntrack -L output.

use:
conntrack -L | php conntrack-monitor.php
conntrack -L | php conntrack-monitor.php m 400
conntrack -L | php conntrack-monitor.php minimum 400
conntrack -L | php conntrack-monitor.php kill 0
php conntrack-monitor.php alias 10.17.240.15 example-domain-alias.tld
php conntrack-monitor.php connection 194.8.253.77 10000
php conntrack-monitor.php subnet 194.8.253.0/24 10000


params:
(m, min, minimum) => conntrack-monitor.php m 500			# 500 minimum connections for output
(kill) => conntrack-monitor.php kill 0 					# 0 = no killing, 1 = killing
(connection) => conntrack-monitor.php connection 194.8.253.77 10000	# limit 10000 connections for ip
(subnet) => conntrack-monitor.php subnet 194.8.253.0/24 10000		# limit 10000 connections for subnet
(sort) => conntrack-monitor.php sort 2					# column for sorting

