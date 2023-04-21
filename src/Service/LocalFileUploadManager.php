<?php

namespace App\Service;

use Behat\Transliterator\Transliterator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalFileUploadManager
{
    public function upload(UploadedFile $uploadfile, $dir):string
    {
        $originalFilename = pathinfo($uploadfile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = strtolower(Transliterator::utf8ToAscii($originalFilename));     
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadfile->guessExtension();
        $uploadfile->move(
            $dir,
            $newFilename
        );
        return $newFilename;
    }
}
