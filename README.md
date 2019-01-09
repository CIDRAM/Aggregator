[![Join the chat at https://gitter.im/CIDRAM/Lobby](https://badges.gitter.im/CIDRAM/Lobby.svg)](https://gitter.im/CIDRAM/Lobby)
[![PHP >= 5.4.0](https://img.shields.io/badge/PHP-%3E%3D%205.4.0-8892bf.svg)](https://maikuolan.github.io/Compatibility-Charts/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
[![PRs Welcome](https://img.shields.io/badge/PRs-Welcome-brightgreen.svg)](http://makeapullrequest.com)

## Aggregator.

A stand-alone class implementation of the IPv4+IPv6 IP+CIDR aggregator from CIDRAM.

---


### How to install:

As a stand-alone PHP class, installing it is exceptionally easy. You can download the file containing the class, [Aggregator.php](src/Aggregator.php), directly from this repository, and copy it to any projects that need it, or, if you'd prefer, you can install it using Composer:

`composer require cidram/aggregator`

*Note: The code in this class is based upon code in the CIDRAM package, but the two are NOT dependent on each other.*

After you've downloaded the file, to allow your projects to use the class, [PSR-4](https://www.php-fig.org/psr/psr-4/) autoloading is preferred (particularly if you're using a large number of different, unrelated classes). If you're installing the class via Composer, all you have to do is `require_once 'vendor/autoload.php';` and everything will be taken care of. Alternatively, if you're installing it manually (or without Composer), and don't want to use a PSR-4 autoloader, you can simply require or include the class into your projects (which may be much easier in many cases) by including the respective statement to point to the class file in the relevant PHP files.

---


### How to use:

The simplest way to use Aggregator is to create a new instance of the class and enter some data to be aggregated as a parameter to the aggregate method. The aggregate method will return an aggregate of the entered data.

Example:
```PHP
<?php
use \CIDRAM\Aggregator\Aggregator;

$Aggregator = new Aggregator();
$Output = $Aggregator->aggregate($Input);
```

Or, if the file `helpers.php` is loaded, this function will be available:
```PHP
$Output = aggregate($Input);
```

*Note: The function `aggregate` will be available if you installed the package via `composer`, but otherwise, you'll need to include the file `src/helpers.php`.*

In the case of the above example, if this is entered as `$Input`:
```
127.0.0.1 Some arbitrary single IPs from here
127.0.0.2
127.0.0.3
1::
1::1
1:2:3:4::0
1:2:3:4::1
1:2:3:4::2
1:2:3:4::3
2002::1
127.0.0.4
127.0.0.5
257.0.0.999 Some arbitrary INVALID single IPs from here
555.666.777.888
2002:abcd:efgh::1
10.0.0.0/9 Some arbitrary CIDRs from here
10.128.0.0/9
10.192.0.0/10
11.128.0.0/10
11.192.0.0/10
12.0.0.0/9
12.128.0.0/9
13.0.0.0/9
13.128.0.0/9
192.168.0.0/8 Some arbitrary INVALID CIDRs from here
192.168.0.0/9
192.168.0.0/10
192.168.192.0/10
192.169.0.0/10
192.169.64.0/10
1.2.3.4/255.255.255.254 Some arbitrary netmasks from here
2.3.4.5/255.255.255.255
99.99.99.99/255.255.255.255
99.10.10.0/255.255.255.0
99.10.11.0/255.255.255.0
99.8.0.0/255.252.0.0
11.11.11.11/11.11.11.11 Some arbitrary INVALID netmasks from here
255.255.255.254/1.2.3.4
6.7.8.9/255.255.255.254
88.88.88.88/255.255.254.255
Foobar Some garbage data from here
ASDFQWER!@#$
>>HelloWorld<<
SDFSDFSDF
QWEQWEQWE
```

`$Output` will be expected to contain this:
```
1.2.3.4/31
2.3.4.5/32
10.0.0.0/8
11.128.0.0/9
12.0.0.0/7
99.8.0.0/14
99.99.99.99/32
127.0.0.1/32
127.0.0.2/31
127.0.0.4/31
1::/127
1:2:3:4::1/128
1:2:3:4::2/127
2002::1/128
```

Data is newline-delimited and each line represents one item to be aggregated. Aggregator handles IPv4+IPv6 seamlessly, attempts to clean up each item (i.e., remove invalid and superfluous data in order to reduce to a valid IP, CIDR, or netmask), attempts to aggregate the resultant cleaned up data (unreadable and invalid data is rejected), and then returns the resultant aggregated data.

It is possible to obtain more information about each aggregation operation if desired. If "Results" is set to `true` (it is `false` by default), then "NumberEntered" (the total number of lines entered when an operation begins), "NumberRejected" (the number of lines or items "rejected", i.e., perceived as invalid, or unreadable; note that this number will also also duplicate items, due to that duplicates are stripped along with invalid and superfluous data prior to aggregation), "NumberAccepted" (the number of lines or items accepted for aggregation; i.e., `NumberAccepted = NumberEntered - NumberRejected`), "NumberMerged" (the total number of items aggregated or merged), and "NumberReturned" (the total number of items returned at the end of an operation) will be populated during operation accordingly. These values can be retrieved after each operation from the class instance or object:

```PHP
<?php
use \CIDRAM\Aggregator\Aggregator;

$Aggregator = new Aggregator();
$Aggregator->Results = true;
$Output = $Aggregator->aggregate($Input);
echo $Output;
echo "\n\n";
echo $Aggregator->NumberEntered . "\n";
echo $Aggregator->NumberRejected . "\n";
echo $Aggregator->NumberAccepted . "\n";
echo $Aggregator->NumberMerged . "\n";
echo $Aggregator->NumberReturned . "\n";
```

Generally, it is better to create a new class instance for each aggregation operation. However, if you want to recycle an old instance, and want to continue to retrieve these values after each operation, you can reset these values to their initial state between operations by using the "resetNumbers" method:

```PHP
$Aggregator->resetNumbers();
```

Regardless of whether "Results" is `true` or `false`, after each aggregation operation, "ProcessingTime" will be available, in case you want to know how much was consumed during the operation. This could be useful both for debugging and for general vanity purposes.

Example:
```PHP
<?php
use \CIDRAM\Aggregator\Aggregator;

$Aggregator = new Aggregator();
$Output = $Aggregator->aggregate($Input);
echo $Aggregator->ProcessingTime . "\n";
```

Additionally, "ExpandIPv4" and "ExpandIPv6" public methods are provided with the class, and they function in exactly the same way their CIDRAM package closure counterparts. Calling either of these with an IPv4 or IPv6 IP address respectively will return an array containing the potential factors for the given IP address. The potential factors are all possible subnets (or CIDRs) that the given IP address is a member of. When a valid IP address is supplied, "ExpandIPv4" and "ExpandIPv6" and should return an array with 32 and 128 elements respectively.

If you want Aggregator to return results as netmasks instead of CIDRs, you can instantiate the object with a parameter value of `1`, like this:
```PHP
<?php
use \CIDRAM\Aggregator\Aggregator;

$Aggregator = new Aggregator(1);
$Output = $Aggregator->aggregate($Input);
```

In the case of the example input mentioned earlier, the output should look something like this:
```
1.2.3.4/255.255.255.254
2.3.4.5/255.255.255.255
10.0.0.0/255.0.0.0
11.128.0.0/255.128.0.0
12.0.0.0/254.0.0.0
99.8.0.0/255.252.0.0
99.99.99.99/255.255.255.255
127.0.0.1/255.255.255.255
127.0.0.2/255.255.255.254
127.0.0.4/255.255.255.254
1::/ffff:ffff:ffff:ffff:ffff:ffff:ffff:fffe:0
1:2:3:4::1/ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff:0
1:2:3:4::2/ffff:ffff:ffff:ffff:ffff:ffff:ffff:fffe:0
2002::1/ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff:0
```

---


### Other information:

#### Licensing:
Licensed as [GNU General Public License version 2.0](https://github.com/CIDRAM/Aggregator/blob/master/LICENSE.txt) (GPLv2).

#### For support:
Please use the issues page of this repository.

---


Last Updated: 9 January 2019 (2019.01.09).
