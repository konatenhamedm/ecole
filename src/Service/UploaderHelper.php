<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{

    private $uploadsPath;
    private $targetDir;

    public function __construct(string $uploadsPath,$targetDir)
    {
        $this->uploadsPath = $uploadsPath;
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file) {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDir(), $fileName);
        return $fileName;
    }

    public function getTargetDir() {
        return $this->targetDir;
    }

    public function uploadImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath . '/images';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return $newFilename;
    }
}
