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

    public function testAcceptsOnlySquareImagesWithArmaSizes(): void
    {
        self::assertTrue(LogoService::hasAllowedDimensions($this->image(64, 64)));
        self::assertTrue(LogoService::hasAllowedDimensions($this->image(256, 256)));
        self::assertFalse(LogoService::hasAllowedDimensions($this->image(50, 50)));
        self::assertFalse(LogoService::hasAllowedDimensions($this->image(64, 32)));
        self::assertFalse(LogoService::hasAllowedDimensions(__FILE__));
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

    public function testIgnoresLogoNamesWithPathCharacters(): void
    {
        $logos = new LogoService($this->publicPath, 'armake');
        $squad = new Squad(1, 1, 'private', '../../etc', 'T', 'N', null, null, null);

        self::assertNull($logos->pngPath($squad));
        $logos->delete('../uploads');
        self::assertDirectoryExists($this->publicPath . '/uploads/logos');
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
