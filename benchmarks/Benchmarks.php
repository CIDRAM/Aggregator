<?php
require '/path/to/the/class/Aggregator/src/Aggregator.php';

use \CIDRAM\Aggregator\Aggregator;

$Aggregator = new Aggregator();

$Monitoring = ['Parse' => 0, 'Copy' => ''];
$Aggregator->callbacks['newParse'] = function () use (&$Monitoring) {
    if ($Monitoring['Parse'] !== 0) {
        echo "\r" . $Monitoring['Copy'];
    }
    $Monitoring['Parse']++;
    $Monitoring['Tick'] = 0;
    $Monitoring['Timer'] = 0;
};
$Aggregator->callbacks['newTick'] = function () use (&$Monitoring) {
    $Monitoring['Tick']++;
    $Monitoring['Timer']++;
    if ($Monitoring['Tick'] >= $Monitoring['Measure']) {
        $Monitoring['Measure']++;
    }
    if ($Monitoring['Timer'] > 100) {
        $Monitoring['Timer'] = 0;
        $Percent = floor(($Monitoring['Tick'] / $Monitoring['Measure']) * 10000) / 100;
        echo "\r" . $Monitoring['Copy'] . '<Parse ' . $Monitoring['Parse'] . '> ' . $Percent . '%';
    }
};

echo "Aggregator Benchmarks.\n===";

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv4-1k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv4 1K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 500; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . "/32\n";
    }
    for ($Iteration = 0; $Iteration < 500; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . ".0/24\n";
    }
    file_put_contents(__DIR__ . '/ipv4-1k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv4-1k-in.txt');
}
echo "\rAggregating 1,000 arbitrary IPv4 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 1000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv4-1k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv6-1k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv6 1K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 500; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/64\n";
    }
    for ($Iteration = 0; $Iteration < 500; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/32\n";
    }
    file_put_contents(__DIR__ . '/ipv6-1k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv6-1k-in.txt');
}
echo "\rAggregating 1,000 arbitrary IPv6 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 1000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv6-1k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv4-5k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv4 5K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . "/32\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . "/25\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . ".0/24\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . ".0/17\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . ".0.0/16\n";
    }
    file_put_contents(__DIR__ . '/ipv4-5k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv4-5k-in.txt');
}
echo "\rAggregating 5,000 arbitrary IPv4 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 5000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv4-5k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv6-5k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv6 5K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/64\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/48\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . (rand(0, 1) ? '' : ':8000') . "::/33\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/32\n";
    }
    for ($Iteration = 0; $Iteration < 1000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . "::/16\n";
    }
    file_put_contents(__DIR__ . '/ipv6-5k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv6-5k-in.txt');
}
echo "\rAggregating 5,000 arbitrary IPv6 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 5000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv6-5k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv4-10k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv4 10K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . "/32\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . "/25\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . ".0/24\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . ".0/17\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . ".0.0/16\n";
    }
    file_put_contents(__DIR__ . '/ipv4-10k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv4-10k-in.txt');
}
echo "\rAggregating 10,000 arbitrary IPv4 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 10000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv4-10k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv6-10k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv6 10K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/64\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/48\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . (rand(0, 1) ? '' : ':8000') . "::/33\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/32\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . "::/16\n";
    }
    file_put_contents(__DIR__ . '/ipv6-10k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv6-10k-in.txt');
}
echo "\rAggregating 10,000 arbitrary IPv6 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 10000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv6-10k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv4-20k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv4 20K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 10000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . "/32\n";
    }
    for ($Iteration = 0; $Iteration < 3000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . "/25\n";
    }
    for ($Iteration = 0; $Iteration < 3000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . ".0/24\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . ".0/17\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . ".0.0/16\n";
    }
    file_put_contents(__DIR__ . '/ipv4-20k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv4-20k-in.txt');
}
echo "\rAggregating 20,000 arbitrary IPv4 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 20000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv4-20k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv6-20k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv6 20K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 10000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/64\n";
    }
    for ($Iteration = 0; $Iteration < 3000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/48\n";
    }
    for ($Iteration = 0; $Iteration < 3000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . (rand(0, 1) ? '' : ':8000') . "::/33\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/32\n";
    }
    for ($Iteration = 0; $Iteration < 2000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . "::/16\n";
    }
    file_put_contents(__DIR__ . '/ipv6-20k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv6-20k-in.txt');
}
echo "\rAggregating 20,000 arbitrary IPv6 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 20000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv6-20k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv4-50k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv4 50K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . "/32\n";
    }
    for ($Iteration = 0; $Iteration < 10000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . "/25\n";
    }
    for ($Iteration = 0; $Iteration < 10000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . ".0/24\n";
    }
    for ($Iteration = 0; $Iteration < 5000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . ".0/17\n";
    }
    for ($Iteration = 0; $Iteration < 5000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . ".0.0/16\n";
    }
    file_put_contents(__DIR__ . '/ipv4-50k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv4-50k-in.txt');
}
echo "\rAggregating 50,000 arbitrary IPv4 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 50000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv4-50k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv6-50k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv6 50K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/64\n";
    }
    for ($Iteration = 0; $Iteration < 10000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/48\n";
    }
    for ($Iteration = 0; $Iteration < 10000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . (rand(0, 1) ? '' : ':8000') . "::/33\n";
    }
    for ($Iteration = 0; $Iteration < 5000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/32\n";
    }
    for ($Iteration = 0; $Iteration < 5000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . "::/16\n";
    }
    file_put_contents(__DIR__ . '/ipv6-50k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv6-50k-in.txt');
}
echo "\rAggregating 50,000 arbitrary IPv6 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 50000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv6-50k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv4-100k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv4 100K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . "/32\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . "/25\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . ".0/24\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . '.' . (rand(0, 1) ? '0' : '128') . ".0/17\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= rand(0, 255) . '.' . rand(0, 255) . ".0.0/16\n";
    }
    file_put_contents(__DIR__ . '/ipv4-100k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv4-100k-in.txt');
}
echo "\rAggregating 100,000 arbitrary IPv4 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 100000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv4-100k-output.txt', $Output);

echo "\n\n";
if (!file_exists(__DIR__ . '/ipv6-100k-in.txt')) {
    echo 'Generating arbitrary dataset (IPv6 100K) ...';
    $Data = '';
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/64\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/48\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . (rand(0, 1) ? '' : ':8000') . "::/33\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . ':' . dechex(rand(1, 65535)) . "::/32\n";
    }
    for ($Iteration = 0; $Iteration < 20000; $Iteration++) {
        $Data .= dechex(rand(1, 65535)) . "::/16\n";
    }
    file_put_contents(__DIR__ . '/ipv6-100k-in.txt', $Data);
} else {
    $Data = file_get_contents(__DIR__ . '/ipv6-100k-in.txt');
}
echo "\rAggregating 100,000 arbitrary IPv6 CIDRs ...\n";
$Average = 0;
for ($Iteration = 1; $Iteration <= 3; $Iteration++) {
    $Aggregator->resetNumbers();
    $Monitoring['Measure'] = 100000;
    $Monitoring['Parse'] = 0;
    $Monitoring['Copy'] = 'Iteration ' . $Iteration . ': ';
    $Time = microtime(true);
    $Output = $Aggregator->aggregate($Data);
    $Time = microtime(true) - $Time;
    $Average += $Time;
    echo "\rIteration " . $Iteration . ': ' . $Time . " seconds\n";
}
echo 'Average time: ' . ($Average / 3) . " seconds\n";
file_put_contents(__DIR__ . '/ipv6-100k-output.txt', $Output);
