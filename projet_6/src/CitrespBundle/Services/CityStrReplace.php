<?php
// src/CitrespBundle/Services/CityStrReplace.php

namespace CitrespBundle\Services;


class CityStrReplace
{
  public function replace($name)
  {
    $city_str = str_replace(['à', 'â', 'ä'], 'a', $name);
    $city_str = str_replace(['é', 'è', 'ê', 'ë'], 'e', $city_str);
    $city_str = str_replace(['î', 'ï'], 'i', $city_str);
    $city_str = str_replace(['ô', 'ö'], 'o', $city_str);
    $city_str = str_replace(['ù', 'û', 'ü'], 'u', $city_str);
    $city_str = str_replace('ÿ', 'y', $city_str);
    $city_str = str_replace('ç', 'c', $city_str);

    $city_str = str_replace('-', ' ', $city_str);

    return $city_str;
  }
}
