<?php

declare(strict_types=1);

/**
 * Creates the missing PAA files of logos that only have a PNG, see LogoService::rebuild().
 *
 * Usage: php bin/rebuild-logos.php [--public-path=<dir>] [--limit=<n>]
 * Prints one line per logo and exits with 1 if any logo failed.
 */

use Armasquads\Config;
use Armasquads\Service\LogoService;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt('', ['public-path:', 'limit:']);
$config = Config::fromEnvironment(dirname(__DIR__));
$publicPath = is_string($options['public-path'] ?? null) ? $options['public-path'] : $config->publicPath;
$limit = is_string($options['limit'] ?? null) ? (int) $options['limit'] : PHP_INT_MAX;

$logos = new LogoService($publicPath, $config->armakeBinary);
$directory = $publicPath . '/uploads/logos';
if (!is_dir($directory)) {
    fwrite(STDERR, "no logo directory at $directory\n");
    exit(2);
}
$size = static fn (string $file): string => implode('x', array_slice(getimagesize($file) ?: [0, 0], 0, 2));
$counts = ['ok' => 0, 'failed' => 0];

foreach (scandir($directory) ?: [] as $logo) {
    $pngFile = "$directory/$logo/$logo.png";
    if ($limit <= 0) {
        break;
    }
    if (!is_file($pngFile) || is_file("$directory/$logo/$logo.paa")) {
        continue;
    }
    $limit--;

    $before = $size($pngFile);
    $result = $logos->rebuild($logo) ? 'ok' : 'failed';
    $counts[$result]++;
    printf("%-6s %s %s -> %s\n", $result, $logo, $before, $size($pngFile));
}

printf("ok: %d, failed: %d\n", $counts['ok'], $counts['failed']);
exit($counts['failed'] > 0 ? 1 : 0);
