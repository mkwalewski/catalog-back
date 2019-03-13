<?php

namespace AppBundle\Service;

use AppBundle\Entity\Files;
use AppBundle\Entity\FilesDisks;

class Catlog
{
    const startTime = 15;
    const excludedFolders = ['$RECYCLE.BIN','System Volume Information'];
    const imagesExtensions = ['jpg','jpeg'];
    const videoExtensions = ['avi','flv','mkv','mov','mp4','mpg','mpeg','ogv','ogg','wmv'];

    public function __construct ($em, $dir, $session)
    {
        $this->em = $em;
        $this->dir = $dir;
        $this->session = $session;
    }

    public function getPartitions ()
    {
        $disks = [];
        $output = shell_exec('wmic logicaldisk get name');

        if ($output)
        {
            foreach (explode ("\r\n", $output) as $item)
            {
                if (preg_match('#([A-Z]{1}\:)#ui', $item, $matches))
                {
                    $disks[] = $matches[1];
                }
            }
        }

        return $disks;
    }

    public function getFolders ($path)
    {
        $folders = [];

        if ($path && is_dir($path))
        {
            foreach (scandir($path) as $file)
            {
                if ($file == '.' || $file == '..' || in_array($file, self::excludedFolders))
                {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir ($fullPath))
                {
                    $folders[] = $file;
                }
            }
        }

        return $folders;
    }

    public function getFiles ($path)
    {
        $files = [];

        if ($path && is_dir($path))
        {
            foreach (scandir($path) as $file)
            {
                if ($file == '.' || $file == '..')
                {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_file ($fullPath))
                {
                    $files[] = $fullPath;
                }
            }
        }

        return $files;
    }

    public function addCatalog ($name, $path)
    {
        $id = 0;
        $files = [];

        if ($name && $path)
        {
            $disk = new FilesDisks();
            $disk->setType(1);
            $disk->setName($name);
            $disk->setPath($path);
            $this->em->persist($disk);
            $this->em->flush();
            $id = $disk->getId();
            $files = $this->getFiles($path);
        }

        $data = [
            'id' => $id,
            'files' => $files
        ];

        return $data;
    }

    public function addCatalogFile ($catalogDiskId, $path)
    {
        //@TODO
        try
        {
            if ($catalogDiskId && $path)
            {
                $data = $this->getBasicData($path);
                if (in_array($data['extension'], self::videoExtensions))
                {
                    $data['type'] = 'video';
                    $data['md5'] = $this->calcMD5($path);
                    $data = array_merge($data, $this->getVideoData($path));
                }
                elseif (in_array($data['extension'], self::imagesExtensions))
                {
                    $data['type'] = 'image';
                    $data['md5'] = $this->calcMD5($path);
                    $data = array_merge($data, $this->getImageData($path));
                }
                else
                {
                    $data['type'] = 'other';
                    $data['md5'] = NULL;
                }
                $filesDisks = $this->em->getRepository('AppBundle:FilesDisks')->find($catalogDiskId);
                $file = new Files();
                $file->setFilesDisks($filesDisks);
                $file->setMd5($data['md5']);
                $file->setFolder($data['folder']);
                $file->setFilename($data['filename']);
                $file->setExtension($data['extension']);
                $file->setCreatedTime($data['created_time']);
                $file->setModifiedTime($data['modified_time']);
                $file->setSize($data['size']);
                $file->setType($data['type']);
                $file->setLength(isset($data['length']) ? $data['length'] : 0);
                $file->setVideoFormat(isset($data['video_format']) ? $data['video_format'] : NULL);
                $file->setVideoWidth(isset($data['video_width']) ? $data['video_width'] : NULL);
                $file->setVideoHeight(isset($data['video_height']) ? $data['video_height'] : NULL);
                $file->setVideoAspect(isset($data['video_aspect']) ? $data['video_aspect'] : NULL);
                $file->setVideoBitrate(isset($data['video_bitrate']) ? $data['video_bitrate'] : NULL);
                $file->setVideoFps(isset($data['video_fps']) ? $data['video_fps'] : NULL);
                $file->setAudioFormat(isset($data['audio_format']) ? $data['audio_format'] : NULL);
                $file->setAudioCodec(isset($data['audio_codec']) ? $data['audio_codec'] : NULL);
                $file->setAudioBitrate(isset($data['audio_bitrate']) ? $data['audio_bitrate'] : NULL);
                $file->setAudioRate(isset($data['audio_rate']) ? $data['audio_rate'] : NULL);
                $file->setAudioNch(isset($data['audio_nch']) ? $data['audio_nch'] : NULL);
                $file->setImageWidth(isset($data['image_width']) ? $data['image_width'] : NULL);
                $file->setImageHeight(isset($data['image_height']) ? $data['image_height'] : NULL);
                $file->setImageMime(isset($data['image_mime']) ? $data['image_mime'] : NULL);
                $file->setFavorite(0);
                $this->em->persist($file);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', 'Sukces');
            }
        }
        catch (\Exception $exception)
        {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }
    }

    public function cancelAddCatalogFile ($catalogDiskId)
    {
        //@TODO
    }

    private function calcMD5 ($fullPath)
    {
        $handle = fopen ($fullPath, "rb");
        $content = fread ($handle, 1024);
        fclose ($handle);

        return md5($content);
    }

    private function getBasicData ($fullPath)
    {
        return [
            'folder' => pathinfo($fullPath, PATHINFO_DIRNAME),
            'filename' => pathinfo($fullPath, PATHINFO_FILENAME),
            'extension' => strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)),
            'created_time' => new \DateTime(date('Y-m-d H:i:s', filectime($fullPath))),
            'modified_time' => new \DateTime(date('Y-m-d H:i:s', filemtime($fullPath))),
            'size' => sprintf('%u', filesize($fullPath))
        ];
    }

    private function getVideoData ($fullPath)
    {
        exec('C:\\mplayer\\mplayer -vo jpeg:outdir=temp -ao null -frames 1 -ss ' . self::startTime . ' -endpos 0 -identify "' . $fullPath . '" 2>&1', $out);
        preg_match_all('#ID_(\w+)=(.+)#', join("\n", $out), $output);
        $data = [
            'length' => 0,
            'video_format' => NULL,
            'video_width' => NULL,
            'video_height' => NULL,
            'video_aspect' => NULL,
            'video_bitrate' => NULL,
            'video_fps' => NULL,
            'audio_format' => NULL,
            'audio_codec' => NULL,
            'audio_bitrate' => NULL,
            'audio_rate' => NULL,
            'audio_nch' => NULL
        ];
        foreach ($output[1] as $key => $value) {
            switch ($value) {
                case 'VIDEO_FORMAT':
                case 'AUDIO_CODEC':
                case 'AUDIO_FORMAT':
                    $data[strtolower($value)] = (string) $output[2][$key];
                    break;
                case 'VIDEO_WIDTH':
                case 'VIDEO_HEIGHT':
                case 'VIDEO_BITRATE':
                case 'VIDEO_FPS':
                case 'AUDIO_BITRATE':
                case 'AUDIO_RATE':
                case 'AUDIO_NCH':
                case 'LENGTH':
                    $data[strtolower($value)] = (int) $output[2][$key];
                    break;
                case 'VIDEO_ASPECT':
                    $data[strtolower($value)] = (float) $output[2][$key];
                    break;
            }
        }

        return $data;
    }

    private function getImageData ($fullPath)
    {
        $data = [
            'image_width' => NULL,
            'image_height' => NULL,
            'image_mime' => NULL
        ];
        $info = getimagesize($fullPath);
        if ($info)
        {
            $data['image_width'] = $info[0];
            $data['image_height'] = $info[1];
            $data['image_mime'] = $info['mime'];
        }

        return $data;
    }
}