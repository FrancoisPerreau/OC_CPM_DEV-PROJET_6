<?php
// src/CitrespBundle/Services/CityStrReplace.php

namespace CitrespBundle\Services;


class CityStrReplace
{
  public function replace($name)
  {
    $city_str = str_replace(['â', 'â', 'à'], 'a', $name);
    $city_str = str_replace(['é', 'è', 'ê', 'ê'], 'e', $city_str);
    $city_str = str_replace(['î', 'ï'], 'i', $city_str);
    $city_str = str_replace(['ô', 'ö'], 'o', $city_str);
    $city_str = str_replace(['û', 'û'], 'u', $city_str);

    return $city_str;
  }
}
