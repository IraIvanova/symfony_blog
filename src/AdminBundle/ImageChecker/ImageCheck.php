<?php

namespace AdminBundle\ImageChecker;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCheck
{
    protected $supportImageTypeList;

    public function __construct($typeList)
    {
        $this->supportImageTypeList= $typeList;
    }

    public function check(UploadedFile $photoFile)
    {
        $checkTrue = false;
        $mimeType= $photoFile->getClientMimeType();
        foreach($this->supportImageTypeList as $imageType)
        {
            if( $mimeType == $imageType[1])
            {
                $checkTrue = true;
            }
        }
        if($checkTrue !== true){
            throw new \InvalidArgumentException("Mime type not found =(");
        }

        $fileExtension = $photoFile->getClientOriginalExtension();
        foreach($this->supportImageTypeList as $imageType)
        {
            if($fileExtension == $imageType[0])
            {
                $check =true;
            }
        }
        if($checkTrue !== true ){
            throw new \InvalidArgumentException('File extension is not supported =(');
        }
    }

}