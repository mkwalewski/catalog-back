<?php

/**
 * @author Michał Walewski
 */

namespace AppBundle\Service;

/**
 * Class Images
 * @package AppBundle\Service
 */
class Images
{
    public function __construct ()
    {

    }

    public function checkImage ($source)
    {
        return file_exists($source) ? TRUE : FALSE;
    }

    public function resizeImage ($source, $destination, $widthDestination, $heightDestination, $compression)
    {
        $success = false;

        try
        {
            #source info
            $sourceInfo = getimagesize ($source);
            list ($width, $height) = $sourceInfo;

            #load source
            switch ($sourceInfo['mime'])
            {
                case 'image/gif':
                    $image = imagecreatefromgif ($source);
                    break;
                case 'image/jpeg':
                    $image = imagecreatefromjpeg ($source);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng ($source);
                    break;
                default:
                    throw new \Exception('Nieobsługiwany format pliku !');
            }

            #calcaulate new width and height
            $xRatio = $widthDestination / $width;
            $yRatio = $heightDestination / $height;
            if (($width <= $widthDestination) && ($height <= $heightDestination))
            {
                $widthDestination = $width;
                $heightDestination = $height;
            }
            elseif (($xRatio * $height) < $heightDestination)
            {
                $heightDestination = ceil($xRatio * $height);
            }
            else
            {
                $widthDestination = ceil($yRatio * $width);
            }

            #resize
            $imageResized = imagecreatetruecolor ($widthDestination, $heightDestination);
            imagecopyresampled ($imageResized, $image, 0, 0, 0, 0, $widthDestination, $heightDestination, $width, $height);

            #remove old file
            if (file_exists($destination))
            {
                unlink($destination);
            }

            #save
            $compression = (int) $compression;
            switch ($sourceInfo['mime'])
            {
                case 'gif':
                case 'image/gif':
                    $success = ImageGIF ($imageResized, $destination);
                    break;
                case 'jpg':
                case 'jpeg':
                case 'image/jpeg':
                    $success = ImageJPEG ($imageResized, $destination, $compression);
                    break;
                case 'png':
                case 'image/png':
                    $success = ImagePNG ($imageResized, $destination, $compression);
                    break;
            }

            #destroy
            imagedestroy ($image);
            imagedestroy ($imageResized);

            return $success;
        }
        catch (\Exception $exception)
        {
            return $exception->getMessage();
        }
    }
}