<?php
/*
 *  WFS-DescripeFeatureType
 */
namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;


class DescripeFeatureType extends ControllerBase 
{

    public static function descripefeaturetype($request) 
    { 
        $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
        $layerList = [];
        foreach ($contentTypes as $contentType) 
        {
            $definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', $contentType->id());
            foreach($definitions as $definition)
            {
                if($definition->getType() === 'geofield')
                {
                    echo $contentType->id().'<br>'; //im XML dann name
                    echo $contentType->label().'<br>'; //im XML dann Title
                    //echo $contentType->description().'<br>'; //im XML dann Abstract
                    array_push($layerList,$contentType->id());
                }
            }
        }
        var_dump($layerList);
    }

}