<?php
 /*
  *  Main Module
  */
namespace Drupal\drupal8ogc\Controller;


use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

 
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
        $form['base']['xmlheader'] = array(
            '#type' => 'textarea',
            '#title' => $this->t('actual XML-Header'),
            '#default_value' => $config->get('xmlheader'),
        );
        $form['base']['getprovider'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('actual ServiceProvider'),
            '#value' => $this->getServiceProviderName($config->get('provider')),
        );
        $form['base']['setprovider'] = array(
            '#type' => 'select',
            '#title' => $this->t('choose Provider'),
            '#options' => $this->createSelectUsers(),
        );
        return parent::buildForm($form, $form_state);
    }
    
    public function submitForm(array &$form, FormStateInterface $form_state)
    {   
        
        dpm($form_state->getValue('xmlheader'));
        dpm($form_state->getValue('setprovider'));
        $this->config('drupal8ogc.settings')
            ->set('provider',$form_state->getValue('setprovider'))
            ->set('xmlheader',$form_state->getValue('xmlheader'))    
            ->save();
        parent::submitForm($form, $form_state);
    }
    
    public function validateForm(array &$form, FormStateInterface $form_state) 
    {
       
    }
    public function createSelectUsers()
    {
        $ids = \Drupal::entityQuery('user')
            ->condition('status', 1)
            ->execute();
        $users = User::loadMultiple($ids);
        $options = array();
        foreach($users as $user)
        {
            $options[$user->id()] = $user->get('name')->value.', '.$user->get('field_ogc_providername')->value;
        }
        return $options;
    }
    
    public function getServiceProviderName($id)
    {
        $user = User::load($id);
        return $user->get('name')->value.', '.$user->get('field_ogc_providername')->value;
    }
}