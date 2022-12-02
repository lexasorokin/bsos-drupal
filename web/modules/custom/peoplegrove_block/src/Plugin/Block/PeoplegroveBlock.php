<?php

namespace Drupal\peoplegrove_block\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a 'Peoplegrove Block' Block.
 *
 * @Block(
 *   id = "peoplegrove_block",
 *   admin_label = @Translation("Peoplegrove block"),
 *   category = @Translation("Peoplegrove"),
 * )
 */
class PeoplegroveBlock extends BlockBase implements BlockPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $config = $this->getConfiguration();
        // $codimth_copyright = $config['codimth_copyright'];
        
        
        $users = array(
           array(
               'personid' => intval($config['person1_id']),
               'identifier' => $config['person1_identifier']
           ),
      
           array(
               'personid' => intval($config['person2_id']),
               'identifier' => $config['person2_identifier']
           ),
      
           array(
               'personid' => intval($config['person3_id']),
               'identifier' => $config['person3_identifier']
           )
        );
        
        $responses = [];
    
        foreach ($users as $user){
         
         $url = "https://api.peoplegrove.com/v1/user/" . $user['personid'];
        
          // Use the API key to authorize
          $context = stream_context_create(array(
            'http' => array(
                'header'  => "authorization: bearer api.8649843d1dcde1ae300745f0d6e9a11d"
            )
          ));
          
          // Get the feed
          $data = file_get_contents($url, false, $context);
        
          // JSON DECODE
          $json = json_decode($data);
          
          // var_dump($json);
          
          // Manually set the identifier
          $json->identifier = $user['identifier'];
          
          // Add this object to the final array
          $responses[] = $json;
          
        }
        
        $return_string = '<div id="peoplecarousel" class="carousel slide" data-ride="carousel"><div class="carousel-inner">';
        
        $i = 0;
        foreach ($responses as $response){ 
          
          $i++;
          
          if($i == 1) { 
            $return_string .= '<div class="p-card item active">'; 
          } else {
            $return_string .= '<div class="p-card item">';
          }
          
          $return_string .= '<img class="circle" src="' . $response->photoUrl . '">';
          
          $return_string .= '<p class="fullname">' . $response->firstName . ' ' . $response->lastName . '</p>';
          
          $return_string .= '<p class="headline">' . $response->headline . '</p>';
          
          $return_string .= '<p class="location">' . $response->location . '</p>';
          
          $return_string .= '<p><a href="https://terrapinsconnect.umd.edu/hub/umd/person?&userProfile=' . $response->identifier . '" target="_blank" class="button">Connect Now</a></p>';
          
          $return_string .= '</div>';
          
        }
        
        $return_string .= '</div><a class="left carousel-control" href="#peoplecarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#peoplecarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a></div><p class="text-center"><a class="view-more-button" href="https://terrapinsconnect.umd.edu/">View More Connections</a></p>';

   //return $return_string;

        return [
            '#markup' => "<div id='peoplegroveblock'>$return_string</div>",
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();

/*
        $current_year = date("Y");
        $codimth_copyright = $this->t('©@year codimTh. All Rights Reserved.', [
            '@year' => $current_year
        ]);

        $form['codimth_copyright'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Copyright'),
            '#description' => $this->t('Write your copyright text.'),
            '#default_value' => isset($config['codimth_copyright']) ? $config['codimth_copyright'] : $codimth_copyright,
        ];
*/
        
        $form['person1_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Person 1 ID'),
            '#description' => $this->t('This is the ID of this person - its a number.'),
            '#default_value' => isset($config['person1_id']) ? $config['person1_id'] : '',
        ];
        $form['person1_identifier'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Person 1 Identifier'),
            '#description' => $this->t('Looks like this: robertduru.'),
            '#default_value' => isset($config['person1_identifier']) ? $config['person1_identifier'] : '',
        ];
        
        $form['person2_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Person 2 ID'),
            '#description' => $this->t('This is the ID of this person - its a number.'),
            '#default_value' => isset($config['person2_id']) ? $config['person2_id'] : '',
        ];
        $form['person2_identifier'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Person 2 Identifier'),
            '#description' => $this->t('Looks like this: robertduru.'),
            '#default_value' => isset($config['person2_identifier']) ? $config['person2_identifier'] : '',
        ];
        
        $form['person3_id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Person 3 ID'),
            '#description' => $this->t('This is the ID of this person - its a number.'),
            '#default_value' => isset($config['person3_id']) ? $config['person3_id'] : '',
        ];
        $form['person3_identifier'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Person 3 Identifier'),
            '#description' => $this->t('Looks like this: robertduru.'),
            '#default_value' => isset($config['person3_identifier']) ? $config['person3_identifier'] : '',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state);
//         $this->setConfigurationValue('codimth_copyright', $form_state->getValue('codimth_copyright'));
        
        $this->setConfigurationValue('person1_id', $form_state->getValue('person1_id'));
        $this->setConfigurationValue('person1_identifier', $form_state->getValue('person1_identifier'));
        $this->setConfigurationValue('person2_id', $form_state->getValue('person2_id'));
        $this->setConfigurationValue('person2_identifier', $form_state->getValue('person2_identifier'));
        $this->setConfigurationValue('person3_id', $form_state->getValue('person3_id'));
        $this->setConfigurationValue('person3_identifier', $form_state->getValue('person3_identifier'));
    }


    /**
     * {@inheritdoc}
     */
    public function blockValidate($form, FormStateInterface $form_state)
    {
/*
        if (empty($form_state->getValue('codimth_copyright'))) {
            $form_state->setErrorByName('codimth_copyright', t('This field is required'));
        }
*/
        if (empty($form_state->getValue('person1_id'))) {
            $form_state->setErrorByName('person1_id', t('This field is required'));
        }
        if (empty($form_state->getValue('person1_identifier'))) {
            $form_state->setErrorByName('person1_identifier', t('This field is required'));
        }
        if (empty($form_state->getValue('person2_id'))) {
            $form_state->setErrorByName('person2_id', t('This field is required'));
        }
        if (empty($form_state->getValue('person2_identifier'))) {
            $form_state->setErrorByName('person2_identifier', t('This field is required'));
        }
        if (empty($form_state->getValue('person3_id'))) {
            $form_state->setErrorByName('person3_id', t('This field is required'));
        }
        if (empty($form_state->getValue('person3_identifier'))) {
            $form_state->setErrorByName('person3_identifier', t('This field is required'));
        }
    }

    /**
     * {@inheritdoc}
     * return 0 If you want to disable caching for this block.
     */
    public function getCacheMaxAge()
    {

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function access(AccountInterface $account, $return_as_object = FALSE)
    {
        return AccessResult::allowedIfHasPermission($account, 'access content');
    }

}