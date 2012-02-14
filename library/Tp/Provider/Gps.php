<?php
/**
 * User: uhon
 * Date: 2012/02/11
 * GitHub: git@github.com:uhon/traxpacking.git
 */


class Tp_Provider_Gps
{
    /**
     * Copied from Gerald Kaszuba (http://stackoverflow.com/questions/2526304/php-extract-gps-exif-data)
     * @param $exifCoord
     * @return float
     */
    function getGpsDigitFormat($exifCoord) {

        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

        return $degrees + $minutes / 60 + $seconds / 3600;

    }

    function gps2Num($coordPart) {

        $parts = explode('/', $coordPart);

        if (count($parts) <= 0)
            return 0;

        if (count($parts) == 1)
            return $parts[0];

        return floatval($parts[0]) / floatval($parts[1]);
    }
}
