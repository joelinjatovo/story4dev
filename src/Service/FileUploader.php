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
    
    /**
     * get_image_location
     * Returns an array of latitude and longitude from the Image file
     * @param $image file path
     * @return multitype:array|boolean
     */
    public function getImageGps(\App\Entity\File $file){
        $image = $this->getTargetDirectory().'/file/'.$file->getName();
        $exif = exif_read_data($image, 0, true);
        if($exif && isset($exif['GPS'])){
            $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
            $GPSLatitude    = $exif['GPS']['GPSLatitude'];
            $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
            $GPSLongitude   = $exif['GPS']['GPSLongitude'];

            $lat_degrees = count($GPSLatitude) > 0 ? gps2Num($GPSLatitude[0]) : 0;
            $lat_minutes = count($GPSLatitude) > 1 ? gps2Num($GPSLatitude[1]) : 0;
            $lat_seconds = count($GPSLatitude) > 2 ? gps2Num($GPSLatitude[2]) : 0;

            $lon_degrees = count($GPSLongitude) > 0 ? gps2Num($GPSLongitude[0]) : 0;
            $lon_minutes = count($GPSLongitude) > 1 ? gps2Num($GPSLongitude[1]) : 0;
            $lon_seconds = count($GPSLongitude) > 2 ? gps2Num($GPSLongitude[2]) : 0;

            $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
            $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;

            $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
            $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

            return array('latitude' => $latitude, 'longitude'=>$longitude);
        }else{
            return false;
        }
    }
    
    private function gps2Num($coordPart){
        $parts = explode('/', $coordPart);
        if(count($parts) <= 0) return 0;
        if(count($parts) == 1) return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }
}