<?php
namespace Frontend\Squads\Service;

class SquadImageService
{

    public function convert($sourceImage, $destinationImage)
    {
        $ch = curl_init();
        $cfile = curl_file_create($sourceImage);
        $data = array(
            'type_convert' => 'image',
            'output_format' => 'DDS',
            'format' => 'DXT5',
            'mipmap' => 'no',
            'keep_prop' => 'true',
            'attach1' => $cfile
        );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'http://foc.mooo.com/converter.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);

        preg_match('#id=([a-z0-9]+)#i', $response, $matches);
        if( count( $matches ) == 2 )
        {
            $imageDDSData = file_get_contents('http://foc.mooo.com/upload/' . trim($matches[1]));
            $imageDDSData = substr($imageDDSData, 128, strlen($imageDDSData));

            $pngImage = new \Imagick($sourceImage);
            $newImageFormat = fopen($destinationImage, 'wb');

            // write typeofpaa
            fwrite($newImageFormat, pack('n', 0x05FF));

            // write tags
            // avarage color
            fwrite($newImageFormat, pack('a4a4IN', "GGAT", "CGVA", 4, 0xFFFFFFFFF));

            // maxct
            fwrite($newImageFormat, pack('a4a4IN', "GGAT", "CXAM", 4, 0xFFFFFFFFF));

            // mark as transparent
            fwrite($newImageFormat, pack('a4a4II', "GGAT", "GALF", 4, 2));

            // offsets
            fwrite($newImageFormat, pack('a4a4IIx62', "GGAT", "SFFO", 0x40, 0x80));

            // image width & heigh
            fwrite($newImageFormat, pack('ss', $pngImage->getimagewidth(), $pngImage->getimageheight()));

            // write first mipmap
            fwrite($newImageFormat, pack('sx', strlen($imageDDSData)));

            // write image
            fwrite($newImageFormat, $imageDDSData);

            // mark end of mipmap
            fwrite($newImageFormat, pack('x4x2'));

            fclose($newImageFormat);

            return $destinationImage;
        }

        return false;
    }

    public function deleteLogo( $logo )
    {
        $logoPath = ROOT_PATH . '/uploads/logos/' . $logo . '/';

        if( !is_dir( $logoPath ) )
            return false;

        $it = new \RecursiveDirectoryIterator($logoPath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it,
            \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($logoPath);



        return true;
    }

    public function saveLogo( $logoSpecs )
    {
        Try {
            $logoName =  md5(microtime(true) . uniqid(microtime(true)));
            $logoPath = ROOT_PATH . '/uploads/logos/' . $logoName . '/';

            mkdir($logoPath, 0777);

            $image = new \Imagick( $logoSpecs['tmp_name'] );

            $saveLogoPath = $logoPath . $logoName . '.png';
            $saveLogoPAAPath = $logoPath . $logoName . '.paa';

            $image->setImageBackgroundColor('transparent');
            $image->stripimage();
            $image->setimageformat('png');
            $image->writeImage($saveLogoPath);
            $image->destroy();
            $image->clear();

            // convert to paa
            $this->convert($saveLogoPath, $saveLogoPAAPath);
        } catch ( \Exception $e )
        {
            return false;
        }

        return $logoName;
    }

}