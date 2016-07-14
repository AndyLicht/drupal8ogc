<?php
 /*
  *  Main Module
  */
namespace Drupal\drupal8ogc\Controller;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

 
class AdminForm extends ConfigFormBase 
{
    
    public function getFormId() 
    {
        return 'drupal8ogc_settings_form';
    }
    
    protected function getEditableConfigNames() 
    {
        return ['drupal8ogc.settings'];  
    }
    
    public function buildForm(array $form, FormStateInterface $form_state) 
    {
        $config = $this->config('drupal8ogc.settings');
        $form['advanced'] = array(
            '#type' => 'vertical_tabs',
            '#title' => t('Settings'),
        );
       
        $form['base'] = array(
            '#type' => 'details',
            '#title' => t('Base Settings'),
            '#group' => 'advanced',
        );

        return parent::buildForm($form, $form_state);
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state)
    {   
        parent::submitForm($form, $form_state);
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) 
    {
       
    }
   
}