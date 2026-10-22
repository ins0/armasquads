<?php

declare(strict_types=1);

namespace Armasquads\Service;

use Armasquads\Model\Squad;
use Imagick;
use Throwable;

/**
 * Squad logos live in public/uploads/logos/<logo>/<logo>.png and get converted to <logo>.paa (the texture format ArmA
 * expects) with armake.
 */
final readonly class LogoService
{
    private const array ALLOWED_SIZES = [16, 32, 64, 128, 256];
    private const array ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png'];
    private const int MAX_SOURCE_SIZE = 4096;

    public function __construct(
        private string $publicPath,
        private string $armakeBinary,
    ) {
    }

    public function pngPath(Squad $squad): ?string
    {
        return $this->existingPath($squad->logo, 'png');
    }

    public function paaPath(Squad $squad): ?string
    {
        return $this->existingPath($squad->logo, 'paa');
    }

    public function absolutePath(string $relativePath): string
    {
        return $this->publicPath . $relativePath;
    }

    /**
     * Returns the logo the game should load: the PAA file, converted on demand, or the PNG if the conversion fails.
     */
    public function serverLogo(Squad $squad): ?string
    {
        if (!$squad->logo) {
            return null;
        }

        if ($this->paaPath($squad) === null) {
            $png = $this->pngPath($squad);
            if ($png === null || !$this->convert($this->absolutePath($png))) {
                return $png;
            }
        }

        return $this->paaPath($squad);
    }

    public static function hasAllowedExtension(string $fileName): bool
    {
        return in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS, true);
    }

    public static function isUsableImage(string $imageFile): bool
    {
        try {
            $image = new Imagick();
            $image->pingImage($imageFile);

            return max($image->getImageWidth(), $image->getImageHeight()) <= self::MAX_SOURCE_SIZE
                && min($image->getImageWidth(), $image->getImageHeight()) > 0;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * The allowed square size closest to the longer side of the image, the larger one on a tie.
     */
    public static function targetSize(int $width, int $height): int
    {
        $longerSide = max($width, $height);
        $best = self::ALLOWED_SIZES[0];
        foreach (self::ALLOWED_SIZES as $size) {
            if (abs($size - $longerSide) <= abs($best - $longerSide)) {
                $best = $size;
            }
        }

        return $best;
    }

    /**
     * Stores an uploaded image as PNG, converts it to PAA and returns the new logo name, or null if either step fails.
     */
    public function save(string $imageFile): ?string
    {
        $logo = bin2hex(random_bytes(16));
        $directory = $this->publicPath . '/uploads/logos/' . $logo;
        $pngFile = $directory . '/' . $logo . '.png';

        try {
            if (!mkdir($directory, 0755)) {
                return null;
            }
            self::writePng($imageFile, $pngFile);
        } catch (Throwable) {
            $this->delete($logo);

            return null;
        }

        if (!$this->convert($pngFile)) {
            $this->delete($logo);

            return null;
        }

        return $logo;
    }

    /**
     * Creates the missing PAA of an existing logo. The PNG is rewritten first if it does not have an allowed size or
     * armake cannot read it (16 bit PNGs, for example). The logo keeps its name and nothing is changed unless the
     * conversion succeeds.
     */
    public function rebuild(string $logo): bool
    {
        $pngFile = $this->publicPath . '/uploads/logos/' . $logo . '/' . $logo . '.png';
        if (!self::isValidName($logo) || !is_file($pngFile)) {
            return false;
        }

        $workDirectory = sys_get_temp_dir() . '/armasquads-rebuild-' . bin2hex(random_bytes(8));
        $workPng = $workDirectory . '/' . $logo . '.png';
        try {
            $image = new Imagick();
            $image->pingImage($pngFile);
            $size = self::targetSize($image->getImageWidth(), $image->getImageHeight());
            if ($image->getImageWidth() === $size && $image->getImageHeight() === $size && $this->convert($pngFile)) {
                return true;
            }

            mkdir($workDirectory, 0700);
            self::writePng($pngFile, $workPng);

            return $this->convert($workPng)
                && copy($workPng, $pngFile)
                && copy(substr($workPng, 0, -4) . '.paa', substr($pngFile, 0, -4) . '.paa');
        } catch (Throwable) {
            return false;
        } finally {
            array_map(unlink(...), glob($workDirectory . '/*') ?: []);
            if (is_dir($workDirectory)) {
                rmdir($workDirectory);
            }
        }
    }

    public function delete(?string $logo): void
    {
        if (!$logo || !self::isValidName($logo)) {
            return;
        }

        $directory = $this->publicPath . '/uploads/logos/' . $logo;
        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) ?: [] as $file) {
            if (is_file($directory . '/' . $file)) {
                unlink($directory . '/' . $file);
            }
        }
        rmdir($directory);
    }

    /**
     * Writes the first frame of an image as PNG, scaled to the closest allowed size and padded with transparency if it
     * is not square.
     */
    private static function writePng(string $imageFile, string $pngFile): void
    {
        $image = new Imagick($imageFile);
        $image->setIteratorIndex(0);
        $image->setImageBackgroundColor('transparent');
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();
        $size = self::targetSize($width, $height);
        if ($width !== $size || $height !== $size) {
            $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
            $image->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1, true);
            $image->extentImage($size, $size, -intdiv($size - $image->getImageWidth(), 2), -intdiv($size - $image->getImageHeight(), 2));
        }
        $image->stripImage();
        $image->setImageFormat('png');
        $image->writeImage('png32:' . $pngFile);
        $image->clear();
    }

    private function convert(string $pngFile): bool
    {
        $paaFile = substr($pngFile, 0, -4) . '.paa';
        $process = proc_open(
            [$this->armakeBinary, 'img2paa', '-z', '-t', 'DXT5', $pngFile, $paaFile],
            [1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']],
            $pipes,
        );

        if ($process === false) {
            return false;
        }
        proc_close($process);

        return is_file($paaFile);
    }

    private function existingPath(?string $logo, string $extension): ?string
    {
        if (!$logo || !self::isValidName($logo)) {
            return null;
        }

        $path = '/uploads/logos/' . $logo . '/' . $logo . '.' . $extension;

        return is_file($this->publicPath . $path) ? $path : null;
    }

    private static function isValidName(string $logo): bool
    {
        return preg_match('/^[A-Za-z0-9]+$/', $logo) === 1;
    }
}
