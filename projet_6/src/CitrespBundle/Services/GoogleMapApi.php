<?php
// src/CitrespBundle/Services/GoogleMapApi.php

namespace CitrespBundle\Services;

class GoogleMapApi
{
    public function geocodeAddress($googleApi, $autocompleteInput)
    {
        $data = [
            'address' => '',
            'lat' => '',
            'lng' => '',
            'city' => '',
            'department' => '',
            'region' => '',
            'country' => '',
            'postal_code' => ''
        ];

        $autocompleteInput = str_replace(" ", "+", $autocompleteInput);

        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=" . $googleApi . "&address=$autocompleteInput&sensor=false&region=fr");
        
        $json = json_decode($json);

         //on enregistre les résultats recherchés
         if ($json->status == 'OK' && count($json->results) > 0)
         {
             $res = $json->results[0];

             //myFormatedAddress : num et nom de rue                
             $myFormatedAddress = null;
             if ($res->address_components[0]->types[0] == 'street_number') {
                 $myFormatedAddress = $res->address_components[0]->long_name . ', ';
             }
             elseif ($res->address_components[0]->types[0] == 'route')
             {
                $myFormatedAddress = $res->address_components[0]->long_name ;
             }

             if ($res->address_components[1]->types[0] == 'route') {
                 $myFormatedAddress = $myFormatedAddress . $res->address_components[1]->long_name;
             }

             if ($myFormatedAddress === null) {
                 return null;
             }
             
             $data['address'] = $myFormatedAddress;

             //latitude/longitude
             $data['lat'] = $res->geometry->location->lat;
             $data['lng'] = $res->geometry->location->lng;
             foreach ($res->address_components as $component) {
                 //ville
                 if ($component->types[0] == 'locality') {
                     $data['city'] = $component->long_name;
                 }
                 //départment
                 if ($component->types[0] == 'administrative_area_level_2') {
                     $data['department'] = $component->long_name;
                 }
                 //région
                 if ($component->types[0] == 'administrative_area_level_1') {
                     $data['region'] = $component->long_name;
                 }
                 //pays
                 if ($component->types[0] == 'country') {
                     $data['country'] = $component->long_name;
                 }
                 //code postal
                 if ($component->types[0] == 'postal_code') {
                     $data['postal_code'] = $component->long_name;
                 }
             }
             return $data;
         }
         else
         {
            return null;
         }
         
    }
}