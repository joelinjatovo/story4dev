<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function chunck(UploadedFile $file)
    {
        try{
            $uploadManager = new \UploadManager\Upload('media');

            if(!empty($chunks)){
                foreach($chunks as $chunk){
                    echo '<p class="success">'.$chunk->getNameWithExtension().' has been uploaded successfully</p>';
                }
            }

            //add validations
            $uploadManager->addValidations([
                new \UploadManager\Validations\Size('2M'), //maximum file size is 2M
                new \UploadManager\Validations\Extension(['jpg','jpeg','png','gif']),
            ]);

            //add callback : remove uploaded chunks on error
            $uploadManager->afterValidate(function($chunk){
                $address=($chunk->getSavePath().$chunk->getNameWithExtension());
                if($chunk->hasError() && file_exists($address)){
                    @unlink($address); //remove current chunk on error
                }
            });
            
            $chunks = $uploadManager->upload($this->getTargetDirectory());
            
        }catch(\UploadManager\Exceptions\Upload $exception){
            //if file exists: (user selects a file)
            if(!empty($exception->getChunk())){
                foreach($exception->getChunk()->getErrors() as $error){
                    echo '<p class="error">'.$error.'</p>';
                }
            }else{
                echo '<p class="error">'.$exception->getMessage().'</p>';
            }
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}