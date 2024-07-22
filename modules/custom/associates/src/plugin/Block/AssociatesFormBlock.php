<?php

namespace Drupal\associates_form\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\associates_form\Form\AssociatesForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 
 *
 * @Block(
 *   id = "associates_form_block",
 *   admin_label = @Translation("Associates Form Block"),
 * )
 */
class AssociatesFormBlock extends BlockBase {

  /**
   * The form builder service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  

  /**
   * 
   *
   * @param array $configuration
   *   
   * @param string $plugin_id
   *   
   * @param mixed $plugin_definition
   *   
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return \Drupal::formBuilder()->getForm('Drupal\associates_form\Form\AssociatesForm');
  }

}
