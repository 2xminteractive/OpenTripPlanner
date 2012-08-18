<?php

$use_cache = FALSE;
$tmpDir = '/tmp/graph-builder/gtfs';
date_default_timezone_set('America/Montreal');

###

if ($argc != 2) {
	die("Usage: $argv[0] [path_to_graph-builder.xml]\n");
}

$conf_file = $argv[1];

exec('grep url ' . escapeshellarg($conf_file) . '  | awk -F\'"\' \'{print $4}\'', $urls);

if (!file_exists($tmpDir)) {
    mkdir($tmpDir, 0777, TRUE);
}

$i = 1;
$total = count($urls);
foreach ($urls as $url) {
    $filePath = getLocalFileForUrl($url);
    
    if (file_exists($filePath)) {
        if ($use_cache) {
            echo "Will use cached GTFS at $filePath instead of downloading $url\n";
            continue;
        }
        unlink($filePath);
    }
    
    echo "[$i/$total] Downloading $url to $filePath ...\n";
    exec('curl -so ' . escapeshellarg($filePath) . ' ' . escapeshellarg($url));
	$i++;
}

$min_dates = array();
$max_dates = array();

chdir($tmpDir);
foreach ($urls as $url) {
    $filePath = getLocalFileForUrl($url);
    if (file_exists("extracted")) {
        exec("rm -rf " . escapeshellarg("$tmpDir/extracted/"));
    }
    mkdir("extracted");
    exec('unzip -d extracted/ ' . escapeshellarg(basename($filePath)));
    exec('find extracted/ -name calendar*', $cal_files);
    
    $max_date = 0;
    $min_date = 20300101;
    foreach ($cal_files as $cal_file) {
        $calendar_data = explode("\n", str_replace("\r", "", file_get_contents($cal_file)));
        unset($start_date_index);
        unset($end_date_index);
        foreach ($calendar_data as $line) {
            if (empty($line)) { continue; }
            $line = explode(",", $line);
            #var_dump($line);
            if (!isset($end_date_index)) {
                for ($i=0; $i<count($line); $i++) {
                    if ($line[$i] == 'end_date' || $line[$i] == 'date') {
                        $end_date_index = $i;
                    }
                    if ($line[$i] == 'start_date' || $line[$i] == 'date') {
                        $start_date_index = $i;
                    }
                }
                continue;
            }
            #var_dump($line);
            $start_date = $line[$start_date_index];
            $end_date = $line[$end_date_index];
            #var_dump($start_date);
            if ($end_date > $max_date) {
                $max_date = $end_date;
            }
            if ($start_date < $min_date) {
                $min_date = $start_date;
            }
        }
    }
    echo "$url:\n";
    echo "  $min_date > $max_date\n";
    $min_dates[] = $min_date;
    $max_dates[] = $max_date;
    exec("rm -rf " . escapeshellarg("$tmpDir/extracted/"));
    unset($cal_files);
}

echo "GTFS data valid from " . formatDate(max($min_dates)) . ' to ' . formatDate(min($max_dates)) . "\n";
$expiration_date = date('Y-m-d', strtotime(formatDate(min($max_dates)) . " - 1 week"));
echo "Graph should be rebuilt on $expiration_date\n";
file_put_contents("$tmpDir/expiration_date.txt", $expiration_date);
echo "  (Wrote expiration date into $tmpDir/expiration_date.txt)\n";

function formatDate($string) {
    return substr($string, 0, 4) . '-' . substr($string, 4, 2) . '-' . substr($string, 6, 2);
}

function getLocalFileForUrl($url) {
    global $tmpDir;
    $url_parts = parse_url($url);
    $cacheFile = $url_parts['host'] . str_replace('/', '_', $url_parts['path']);
    $fileName = $cacheFile . "_gtfs.zip";
    $filePath = "$tmpDir/$fileName";
    return $filePath;
}
?>
