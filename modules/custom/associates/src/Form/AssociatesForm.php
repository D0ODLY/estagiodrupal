<?php

namespace Drupal\associates_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\file\Entity\File;

class AssociatesForm extends FormBase {

  protected $fileSystem;
  protected $messenger;
  protected $logger;
  protected $requestStack;

  public function __construct(FileSystemInterface $file_system, MessengerInterface $messenger, LoggerInterface $logger, RequestStack $request_stack) {
    $this->fileSystem = $file_system;
    $this->messenger = $messenger;
    $this->logger = $logger;
    $this->requestStack = $request_stack;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('messenger'),
      $container->get('logger.factory')->get('associates_form'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'associates_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['excel_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Upload Excel file'),
      '#description' => $this->t('Upload an Excel file in .xls or .xlsx format.'),
      '#upload_location' => 'public://files/',
      '#upload_validators' => [
        'file_validate_extensions' => ['xls xlsx'],
      ],
      '#required' => TRUE,
      '#validators' => [
        [$this, 'validateExcelFile'],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function validateExcelFile($element, &$form_state, $form) {
    $file_id = $form_state->getValue('excel_file')[0];
    if (!$file_id) {
      $form_state->setErrorByName('excel_file', $this->t('Please upload a file.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file_id = $form_state->getValue('excel_file')[0];

    if ($file_id) {
      $file = File::load($file_id);

      if ($file) {
        
        $file->setPermanent();
        $file->save();

        $destination = 'public://files/';
        
        try {
          if (!file_exists($destination)) {
            $this->fileSystem->mkdir($destination, 0775, TRUE);
          }
        } catch (\Exception $e) {
          $this->messenger->addMessage($this->t('Failed to create the directory.'), 'error');
          $this->logger->error('File upload failed: Unable to create destination directory. Error: @error', ['@error' => $e->getMessage()]);
          return;
        }

        try {
          // Move the file to the destination directory.
          $this->fileSystem->move($file->getFileUri(), $destination . $file->getFilename(), FileSystemInterface::EXISTS_REPLACE);

          // Load the Excel file.
          $spreadsheet = IOFactory::load($destination . $file->getFilename());
          $this->messenger->addMessage($this->t('File uploaded successfully.'));
        } catch (\Exception $e) {
          $this->messenger->addMessage($this->t('Failed to process the Excel file.'), 'error');
          $this->logger->error('File upload failed: Unable to process the Excel file. Error: @error', ['@error' => $e->getMessage()]);
        }
      } else {
        $this->messenger->addMessage($this->t('File upload failed.'), 'error');
      }
    } else {
      $this->messenger->addMessage($this->t('File upload failed.'), 'error');
    }
  }

}