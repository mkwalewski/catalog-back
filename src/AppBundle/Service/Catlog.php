<?php

namespace AppBundle\Service;

use AppBundle\Entity\Files;
use AppBundle\Entity\FilesDisks;
use AppBundle\Entity\FilesFrames;
use AppBundle\Entity\FilesGroups;

class Catlog
{
    const startTime = 15;
    const tempDir = 'temp';
    const tempThumbFileName = '00000001.jpg';
    const thumbsDir = 'thumbs';
    const thumbWidth = 300;
    const thumbHeight = 300;
    const thumbCompression = 85;
    const thumbExt = 'jpg';
    const excludedFolders = ['$RECYCLE.BIN','System Volume Information'];
    const audioExtensions = ['mp3'];
    const imagesExtensions = ['jpg','jpeg'];
    const videoExtensions = ['avi','flv','mkv','mov','mp4','mpg','mpeg','ogv','ogg','wmv'];

    public function __construct ($em, $dir, $session, \AppBundle\Service\Images $images)
    {
        $this->em = $em;
        $this->dir = $this->addDirToPath($dir, 'web');
        $this->session = $session;
        $this->images = $images;
    }

    private function addDirToPath ($path, $dir, $addSeparatorAtTheEnd = true)
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $dir = trim($dir, DIRECTORY_SEPARATOR);
        return $addSeparatorAtTheEnd ? $path . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR : $path . DIRECTORY_SEPARATOR . $dir;
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

    public function getFiles ($path, $recursively)
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
                if (is_dir($fullPath) && $recursively)
                {
                    $files = array_merge($files, $this->getFiles($fullPath, $recursively));
                }
                if (is_file ($fullPath))
                {
                    $files[] = $fullPath;
                }
            }
        }

        return $files;
    }

    public function addCatalog ($groupId, $name, $path, $recursively)
    {
        $id = 0;
        $files = [];

        if ($name && $path)
        {
            $filesGroups = $this->em->getRepository('AppBundle:FilesGroups')->find($groupId);
            $disk = new FilesDisks();
            $disk->setName($name);
            $disk->setPath($path);
            $disk->setFilesGroups($filesGroups);
            $this->em->persist($disk);
            $this->em->flush();
            $id = $disk->getId();
            $files = $this->getFiles($path, $recursively);
        }

        $data = [
            'id' => $id,
            'files' => $files
        ];

        return $data;
    }

    public function addGroup ($name)
    {
        if ($name)
        {
            try
            {
                $group = new FilesGroups();
                $group->setName($name);
                $this->em->persist($group);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', 'Pomyślnie dodano grupę');
            }
            catch (\Exception $exception)
            {
                $this->session->getFlashBag()->add('error', $exception->getMessage());
            }
        }
    }

    public function getGroups ()
    {
        $data = [];
        $groups = $this->em->getRepository('AppBundle:FilesGroups')->findAll();

        if ($groups)
        {
            foreach ($groups as $group)
            {
                $data[] = [
                    'id' => $group->getId(),
                    'name' => $group->getName(),
                ];
            }
        }

        return $data;
    }

    public function getCatalogDisksByGroupId ($groupId)
    {
        $data = [];

        if ($groupId)
        {
            $group = $this->em->getRepository('AppBundle:FilesGroups')->find($groupId);

            if ($group)
            {
                $disks = $this->em->getRepository('AppBundle:FilesDisks')->findBy(['FilesGroups'=>$group]);

                if ($disks)
                {
                    foreach ($disks as $disk)
                    {
                        $data[] = [
                            'id' => $disk->getId(),
                            'path' => $disk->getPath(),
                            'name' => $disk->getName(),
                        ];
                    }
                }
            }
        }

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
                elseif (in_array($data['extension'], self::audioExtensions))
                {
                    $data['type'] = 'audio';
                    $data['md5'] = $this->calcMD5($path);
                    $data = array_merge($data, $this->getAudioData($path));
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
                switch ($data['type'])
                {
                    case 'video':
                        $image = $this->addDirToPath($this->dir, self::tempDir) . self::tempThumbFileName;
                        $this->generateThumb($file, $image, true);
                        break;
                    case 'image':
                        $this->generateThumb($file, $path, false);
                        break;
                }
                $this->session->getFlashBag()->add('success', 'Sukces');
            }
        }
        catch (\Exception $exception)
        {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }
    }

    public function deleteCatalogDisk ($catalogDiskId)
    {
        //@TODO
        try
        {
            $filesDisks = $this->em->getRepository('AppBundle:FilesDisks')->find($catalogDiskId);
            if ($filesDisks)
            {
                $files = $this->em->getRepository('AppBundle:Files')->findBy(['FilesDisks'=>$filesDisks]);
                if ($files)
                {
                    foreach ($files as $file)
                    {
                        $filesFrames = $this->em->getRepository('AppBundle:FilesFrames')->findBy(['Files'=>$file]);
                        if ($filesFrames)
                        {
                            foreach ($filesFrames as $frame)
                            {
                                $image = $this->addDirToPath($this->dir, $frame->getImage(), false);
                                if (file_exists ($image))
                                {
                                    unlink($image);
                                }
                                $this->em->remove($frame);
                                $this->em->flush();
                            }
                        }
                        $this->em->remove($file);
                        $this->em->flush();
                    }
                }
                $this->em->remove($filesDisks);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', 'Anulowano');
            }
        }
        catch (\Exception $exception)
        {
            $this->session->getFlashBag()->add('error', $exception->getMessage());
        }
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

    private function getAudioData ($fullPath)
    {
        exec('C:\\mplayer\\mplayer -vo null -ao null -endpos 0 -identify "' . $fullPath . '" 2>&1', $out);
        preg_match_all('#ID_(\w+)=(.+)#', join("\n", $out), $output);
        $data = [
            'length' => 0,
            'audio_format' => NULL,
            'audio_codec' => NULL,
            'audio_bitrate' => NULL,
            'audio_rate' => NULL,
            'audio_nch' => NULL
        ];
        foreach ($output[1] as $key => $value) {
            switch ($value) {
                case 'AUDIO_CODEC':
                case 'AUDIO_FORMAT':
                    $data[strtolower($value)] = (string) $output[2][$key];
                    break;
                case 'AUDIO_BITRATE':
                case 'AUDIO_RATE':
                case 'AUDIO_NCH':
                case 'LENGTH':
                    $data[strtolower($value)] = (int) $output[2][$key];
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

    private function generateThumb ($file, $image, $delete = false)
    {
        if (file_exists ($image))
        {
            $thumb = $this->addDirToPath($this->dir, self::thumbsDir) . $file->getId() . '.' . self::thumbExt;
            $thumbForDb = '/' . self::thumbsDir . '/' . $file->getId() . '.' . self::thumbExt;
            if ($this->images->resizeImage($image, $thumb, self::thumbWidth, self::thumbHeight, self::thumbCompression) === true)
            {
                $filesFrames = new FilesFrames();
                $filesFrames->setFiles($file);
                $filesFrames->setFrame(0);
                $filesFrames->setTime(NULL);
                $filesFrames->setImage($thumbForDb);
                $this->em->persist($filesFrames);
                $this->em->flush();
            }
            if ($delete)
            {
                unlink($image);
            }
        }
    }

    private static function buildTreeByPaths ($paths)
    {
        if (count($paths) > 0)
        {
            $first = array_shift($paths);
            return [$first => self::buildTreeByPaths($paths)];
        }
        elseif (count($paths) === 0)
        {
            return [];
        }
    }

    private static function buildTree ($treePaths, $path = '')
    {
        $tree = [];

        foreach ($treePaths as $key => $folders)
        {
            $item = [
                'text' => $key,
                'value' => $path . $key . '\\',
                'opened' => false
            ];
            if (count($folders) > 0)
            {
                $item['children'] = self::buildTree($folders, $path . $key . '\\');
            }
            $tree[] = $item;

        }
        return $tree;
    }

    public static function extractPathForTree ($tree, $path)
    {
        foreach ($tree as $item)
        {
            if (isset($item['children']))
            {
                if ($item['value'] == $path . '\\')
                {
                    return $item['children'];
                }
                else
                {
                    return self::extractPathForTree($item['children'], $path);
                }
            }
        }
    }

    public function getTreeFolders ($catalogDiskId)
    {
        $tree = [];

        if ($catalogDiskId)
        {
            $catalogDisk = $this->em->getRepository('AppBundle:FilesDisks')->find($catalogDiskId);
            $treeFolders = $this->em->getRepository('AppBundle:Files')->getTreeFolders($catalogDiskId);

            if ($treeFolders)
            {
                foreach ($treeFolders as $row)
                {
                    $paths = explode('\\', $row['folder']);
                    $tree = array_merge_recursive($tree, self::buildTreeByPaths($paths));
                }
                $tree = self::buildTree($tree);
                $tree = self::extractPathForTree($tree, $catalogDisk->getPath());
            }
        }

        return $tree;
    }
}