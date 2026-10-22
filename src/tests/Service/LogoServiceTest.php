<?php

declare(strict_types=1);

namespace Armasquads\Tests\Service;

use Armasquads\Model\Squad;
use Armasquads\Service\LogoService;
use Imagick;
use ImagickPixel;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;

#[RequiresPhpExtension('imagick')]
final class LogoServiceTest extends TestCase
{
    private string $publicPath;

    protected function setUp(): void
    {
        $this->publicPath = sys_get_temp_dir() . '/armasquads-test-' . bin2hex(random_bytes(4));
        mkdir($this->publicPath . '/uploads/logos', 0777, true);
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . escapeshellarg($this->publicPath));
    }

    public function testAcceptsReadableImagesUpTo4096Pixels(): void
    {
        self::assertTrue(LogoService::isUsableImage($this->image(64, 64)));
        self::assertTrue(LogoService::isUsableImage($this->image(50, 30)));
        self::assertTrue(LogoService::isUsableImage($this->image(4096, 8)));
        self::assertFalse(LogoService::isUsableImage($this->image(4097, 8)));
        self::assertFalse(LogoService::isUsableImage(__FILE__));
    }

    public function testTargetSizeIsTheClosestArmaSize(): void
    {
        $cases = [[16, 16, 16], [5, 5, 16], [20, 20, 16], [24, 24, 32], [48, 48, 64], [100, 100, 128], [225, 225, 256], [500, 500, 256], [300, 150, 256], [40, 90, 64]];
        foreach ($cases as [$width, $height, $expected]) {
            self::assertSame($expected, LogoService::targetSize($width, $height), "{$width}x$height");
        }
    }

    public function testSaveScalesAndPadsImagesToTheClosestArmaSize(): void
    {
        $logos = new LogoService($this->publicPath, $this->fakeArmake());
        $logo = $logos->save($this->image(300, 150));
        $png = new Imagick($this->publicPath . "/uploads/logos/$logo/$logo.png");

        self::assertSame([256, 256], [$png->getImageWidth(), $png->getImageHeight()]);
        self::assertSame(0.0, $png->getImagePixelColor(128, 10)->getColorValue(Imagick::COLOR_ALPHA), 'transparent border above');
        self::assertSame(0.0, $png->getImagePixelColor(128, 245)->getColorValue(Imagick::COLOR_ALPHA), 'transparent border below');
        self::assertGreaterThan(0.4, $png->getImagePixelColor(128, 128)->getColorValue(Imagick::COLOR_ALPHA), 'image in the middle');
    }

    public function testSaveKeepsImagesThatAlreadyHaveAnArmaSize(): void
    {
        $logos = new LogoService($this->publicPath, $this->fakeArmake());
        $source = new Imagick($this->image(64, 64));
        $logo = $logos->save($this->image(64, 64));
        $png = new Imagick($this->publicPath . "/uploads/logos/$logo/$logo.png");

        self::assertSame([64, 64], [$png->getImageWidth(), $png->getImageHeight()]);
        self::assertSame($source->getImagePixelColor(10, 10)->getColorAsString(), $png->getImagePixelColor(10, 10)->getColorAsString());
    }

    public function testSaveStoresPngAndPaaAndDeleteRemovesTheDirectory(): void
    {
        $logos = new LogoService($this->publicPath, $this->fakeArmake());
        $logo = $logos->save($this->image(32, 32));
        $squad = new Squad(1, 1, 'private', $logo, 'T', 'N', null, null, null);

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', (string) $logo);
        self::assertSame("/uploads/logos/$logo/$logo.png", $logos->pngPath($squad));
        self::assertSame("/uploads/logos/$logo/$logo.paa", $logos->paaPath($squad));

        $logos->delete($logo);
        self::assertDirectoryDoesNotExist($this->publicPath . "/uploads/logos/$logo");
    }

    public function testSaveReturnsNullAndKeepsNoFilesIfTheConversionFails(): void
    {
        $logos = new LogoService($this->publicPath, '/bin/false');

        self::assertNull($logos->save($this->image(32, 32)));
        self::assertSame([], array_values(array_diff(scandir($this->publicPath . '/uploads/logos'), ['.', '..'])));
    }

    public function testSaveReturnsNullAndKeepsNoFilesIfTheImageCannotBeRead(): void
    {
        $logos = new LogoService($this->publicPath, $this->fakeArmake());

        self::assertNull($logos->save(__FILE__));
        self::assertSame([], array_values(array_diff(scandir($this->publicPath . '/uploads/logos'), ['.', '..'])));
    }

    public function testServerLogoFallsBackToThePngOfALogoWithoutPaa(): void
    {
        $logo = str_repeat('a', 32);
        mkdir($this->publicPath . "/uploads/logos/$logo");
        copy($this->image(32, 32), $this->publicPath . "/uploads/logos/$logo/$logo.png");
        $logos = new LogoService($this->publicPath, '/bin/false');
        $squad = new Squad(1, 1, 'private', $logo, 'T', 'N', null, null, null);

        self::assertNull($logos->paaPath($squad));
        self::assertSame("/uploads/logos/$logo/$logo.png", $logos->serverLogo($squad), 'falls back to the PNG');
    }

    public function testConvertsToPaaWithArmake(): void
    {
        exec('command -v armake', $output, $exitCode);
        if ($exitCode !== 0) {
            self::markTestSkipped('armake is not installed');
        }

        $logos = new LogoService($this->publicPath, 'armake');
        $logo = $logos->save($this->image(64, 64));
        $squad = new Squad(1, 1, 'private', $logo, 'T', 'N', null, null, null);

        $paa = $logos->paaPath($squad);
        self::assertSame("/uploads/logos/$logo/$logo.paa", $paa);
        self::assertSame("\x05\xff", substr((string) file_get_contents($logos->absolutePath((string) $paa)), 0, 2), 'DXT5 PAA header');
    }

    public function testRebuildScalesAnOldLogoAndCreatesItsPaa(): void
    {
        $logo = $this->legacyLogo(225, 225);
        $logos = new LogoService($this->publicPath, $this->fakeArmake());

        self::assertTrue($logos->rebuild($logo));
        $png = new Imagick($this->publicPath . "/uploads/logos/$logo/$logo.png");
        self::assertSame([256, 256], [$png->getImageWidth(), $png->getImageHeight()]);
        self::assertFileExists($this->publicPath . "/uploads/logos/$logo/$logo.paa");
    }

    public function testRebuildKeepsThePngOfALogoThatAlreadyHasAnArmaSize(): void
    {
        $logo = $this->legacyLogo(64, 64);
        $pngFile = $this->publicPath . "/uploads/logos/$logo/$logo.png";
        $before = md5_file($pngFile);

        self::assertTrue((new LogoService($this->publicPath, $this->fakeArmake()))->rebuild($logo));
        self::assertSame($before, md5_file($pngFile));
        self::assertFileExists($this->publicPath . "/uploads/logos/$logo/$logo.paa");
    }

    public function testRebuildChangesNothingIfTheConversionFails(): void
    {
        $logo = $this->legacyLogo(225, 225);
        $pngFile = $this->publicPath . "/uploads/logos/$logo/$logo.png";
        $before = md5_file($pngFile);

        self::assertFalse((new LogoService($this->publicPath, '/bin/false'))->rebuild($logo));
        self::assertSame($before, md5_file($pngFile));
        self::assertSame(["$logo.png"], array_values(array_diff(scandir($this->publicPath . "/uploads/logos/$logo"), ['.', '..'])));
    }

    public function testRebuildRewrites16BitPngsThatArmakeCannotRead(): void
    {
        exec('command -v armake', $output, $exitCode);
        if ($exitCode !== 0) {
            self::markTestSkipped('armake is not installed');
        }

        $logo = bin2hex(random_bytes(16));
        $pngFile = $this->publicPath . "/uploads/logos/$logo/$logo.png";
        mkdir(dirname($pngFile));
        $image = new Imagick();
        $image->newImage(64, 64, new ImagickPixel('gray50'));
        $image->setImageType(Imagick::IMGTYPE_GRAYSCALE);
        $image->setOption('png:bit-depth', '16');
        $image->setOption('png:color-type', '0');
        $image->writeImage('png:' . $pngFile);
        exec('armake img2paa -z -t DXT5 ' . escapeshellarg($pngFile) . ' /dev/null 2>&1', $output, $exitCode);
        self::assertNotSame(0, $exitCode, 'armake cannot read the 16 bit PNG as it is');

        self::assertTrue((new LogoService($this->publicPath, 'armake'))->rebuild($logo));
        self::assertSame(8, (new Imagick($pngFile))->getImageDepth());
        self::assertFileExists($this->publicPath . "/uploads/logos/$logo/$logo.paa");
    }

    public function testRebuildRejectsMissingLogosAndPathCharacters(): void
    {
        $logos = new LogoService($this->publicPath, $this->fakeArmake());

        self::assertFalse($logos->rebuild(str_repeat('b', 32)));
        self::assertFalse($logos->rebuild('../uploads'));
    }

    public function testIgnoresLogoNamesWithPathCharacters(): void
    {
        $logos = new LogoService($this->publicPath, 'armake');
        $squad = new Squad(1, 1, 'private', '../../etc', 'T', 'N', null, null, null);

        self::assertNull($logos->pngPath($squad));
        $logos->delete('../uploads');
        self::assertDirectoryExists($this->publicPath . '/uploads/logos');
    }

    /**
     * A logo as old uploads left it: only a PNG in its directory, in any size.
     */
    private function legacyLogo(int $width, int $height): string
    {
        $logo = bin2hex(random_bytes(16));
        mkdir($this->publicPath . "/uploads/logos/$logo");
        copy($this->image($width, $height), $this->publicPath . "/uploads/logos/$logo/$logo.png");

        return $logo;
    }

    /**
     * Stands in for armake: writes the PAA file it is asked for (img2paa -z -t DXT5 <png> <paa>).
     */
    private function fakeArmake(): string
    {
        $file = $this->publicPath . '/armake';
        file_put_contents($file, "#!/bin/sh\nprintf PAA > \"\$6\"\n");
        chmod($file, 0755);

        return $file;
    }

    private function image(int $width, int $height): string
    {
        $file = $this->publicPath . "/image-{$width}x$height.png";
        $image = new Imagick();
        $image->newImage($width, $height, new ImagickPixel('rgba(200, 30, 30, 0.5)'));
        $image->setImageFormat('png');
        $image->writeImage($file);

        return $file;
    }
}
