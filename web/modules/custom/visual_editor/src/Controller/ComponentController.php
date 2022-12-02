<?php

namespace Drupal\visual_editor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenDialogCommand;
use Drupal\Component\Serialization\Json;

class ComponentController extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The ModalFormExampleController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   */
  public function __construct(FormBuilder $formBuilder) {
    $this->formBuilder = $formBuilder;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('form_builder')
    );
  }

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content(Request $request, $method = 'nojs') {
    $response = new AjaxResponse();
    $content = $request->get('visual_editor_settings');

    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\visual_editor\Form\ComponentForm', $content);
    $content = Json::decode($content);
    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenDialogCommand('#component-settings', t('@component settings', ['@component' => $content['name']]), $modal_form, ['width' => '90%', 'height' => 'auto', 'modal' => true]));

    return $response;
  }

}
