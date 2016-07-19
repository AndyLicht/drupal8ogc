<?php
/*
 *  WFS-DescripeFeatureType
 */
namespace Drupal\drupal8ogc_wfs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Controller\EntityListController;
use Drupal\Core\Routing\RouteMatchInterface;

class DescripeFeatureType extends ControllerBase 
{
    //eventuell durch getFieldMapByFieldType optimieren
    public static function descripefeaturetype($get) 
    { 
        $responseXML = null;
        if(!isset($get['typeNames']) || $get['typeNames'] === '')
        {
            return WFSException::setException(0,null);
        }
        else
        {
            return self::createdescription($get);
        }
    }
    /*    
        //available CTs for WFS
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
                    echo $contentType->getDescription().'<br>'; //im XML dann Abstract
                    array_push($layerList,$contentType->id());
                }
            }
        }
        if(in_array($get['typeNames'], $layerList))
        {
            echo "hier muss jetzt der descripe aufgebaut werden";
        }       
        else
        {
            return WFSException::setException(2,null);
        }
        //var_dump($layerList);
        return $responseXML;
    }*/
    function createdescription($get)
    {
        $responseXML = null;   
        $entity_type_id = 'node';
        $bundle = $get['typeNames'];
        
        foreach (\Drupal::entityManager()->getFieldDefinitions($entity_type_id, $bundle) as $field_name => $field_definition)
        {
            if (!empty($field_definition->getTargetBundle())) 
            {
                $field_definition->getType();
                $field_definition->label();
                $field_definition->id();
                
                $responseXML = $responseXML.$field_definition->id();
            }
        }
        if($responseXML === null)
        {    
            return WFSException::setException(2,null);
        }       
        else
        {
            return $responseXML;
        }

        
    }
}
