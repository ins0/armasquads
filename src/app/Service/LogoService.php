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

    public static function hasAllowedDimensions(string $imageFile): bool
    {
        try {
            $image = new Imagick($imageFile);
            $height = $image->getImageHeight();

            return $height === $image->getImageWidth() && in_array($height, self::ALLOWED_SIZES, true);
        } catch (Throwable) {
            return false;
        }
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

            $image = new Imagick($imageFile);
            $image->setImageBackgroundColor('transparent');
            $image->stripImage();
            $image->setImageFormat('png');
            $image->writeImage('png32:' . $pngFile);
            $image->clear();
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
