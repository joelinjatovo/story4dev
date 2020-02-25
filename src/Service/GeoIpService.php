<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Option;

class GeoIpService
{
    private $geoDirectory;

    public function __construct($geoDirectory)
    {
        $this->geoDirectory = $geoDirectory;
    }
    
    /**
    * throw Exception
    */
    public function getCountry(string $ip)
    {
        $phar = $this->geoDirectory . '/geoip2.phar';
        $db = $this->geoDirectory . '/GeoLite2-Country.mmdb';
        if( file_exists( $phar ) && file_exists( $db ) ) {
            require_once($phar);
            $reader = new \GeoIp2\Database\Reader($db);
            try{
                return $reader->country($ip);
            }catch(\Exception $e){}
            
        }
        
        return null;
    }
    
    /**
    * throw Exception
    */
    public function getCity(string $ip)
    {
        $phar = $this->geoDirectory . '/geoip2.phar';
        $db = $this->geoDirectory . '/GeoLite2-City.mmdb';
        if( file_exists( $phar ) && file_exists( $db ) ) {
            require_once($phar);
            $reader = new \GeoIp2\Database\Reader($db);
            try{
                return $reader->city($ip);
            }catch(\Exception $e){}
        }
        
        return null;
    }
}
