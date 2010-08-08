<?php

/**
 * A Form class designed to be integrated with jQuery Validation
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  form
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */
class sfFormJqueryValidation
  extends sfFormSymfony
  implements sfFormJqueryValidationInterface
{

  protected
    // error message
    $globalErrorMessage = 'This form contains errors',
    // whether to append error class server side
    $useFieldErrorClassServerSide = true,
    // whether to append valid class server side
    $useFieldValidClassServerSide = true,
    // whether to show valid on empty fields
    $useValidClassOnEmptyFields = false,
    $useJqueryValidation = true,
    $jqueryValidationGenerator
  ;


  /**
   * Handles setting up widget schema for required fields asterisks
   * 
   * @see parent
   */
  public function configure()
  {
    $formFormatter = $this->getWidgetSchema()->getFormFormatter();

    if (!$formFormatter instanceOf
        sfWidgetFormSchemaFormatterJqueryValidationInterface
    )
    {
      throw new Exception(
        'Form Formatter must be an instance of'
        . ' sfWidgetFormSchemaFormatterJqueryValidationInterface'
      );
    }

    $formFormatter->setForm($this);

    $this->setUseJqueryValidation(
      sfConfig::get('app_sfJqueryValidationPlugin_jquery_validation_by_default')
    );

    $this->setJqueryValidationGenerator(new sfJqueryValidationGenerator($this));
  }

  /**
   * Method sets error/valid classes to form fields
   * 
   * @see parent
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);

    $this->_appendClassesToFormFields();
  }


  /**
   * @return  string
   */
  public function getGlobalErrorMessage()
  {
    return $this->globalErrorMessage;
  }

  /**
   * @param   string  $globalErrorMessage
   * @return  self
   */
  public function setGlobalErrorMessage($globalErrorMessage)
  {
    $this->globalErrorErrorMessage = $globalErrorMessage;
    
    return $this;
  }

  /**
   * @return  boolean
   */
  public function getUseFieldErrorClassServerSide()
  {
    return $this->useFieldErrorClassServerSide;
  }

  /**
   * @param   boolean $useFieldErrorClassServerSide
   * @return  self
   */
  public function setUseFieldErrorClassServerSide($useFieldErrorClassServerSide)
  {
    $this->useFieldErrorClassServerSide = $useFieldErrorClassServerSide;
    return $this;
  }

  /**
   * @return  boolean
   */
  public function getUseFieldValidClassServerSide()
  {
    return $this->useFieldValidClassServerSide;
  }

  /**
   * @param   boolean $useFieldValidClassServerSide
   * @return  self
   */
  public function setUseFieldValidClassServerSide($useFieldValidClassServerSide)
  {
    $this->useFieldErrorClassServerSide = $useFieldErrorClassServerSide;
    return $this;
  }

  /**
   * @return  boolean
   */
  public function getUseValidClassOnEmptyFields()
  {
    return $this->useValidClassOnEmptyFields;
  }

  /**
   * @param   boolean $useValidClassOnEmptyFields
   * @return  self
   */
  public function setUseValidClassOnEmptyFields($useValidClassOnEmptyFields)
  {
    $this->useValidClassOnEmptyFields = $useValidClassOnEmptyFields;
    return $this;
  }
  
  /**
   * Append classes to the fields
   *
   * @return  void
   */
  protected function _appendClassesToFormFields()
  {
    if (
      $this->getUseFieldErrorClassServerSide()
      ||
      $this->getUseFieldValidClassServerSide()
    )
    {
      $errorClass = $this
        ->getWidgetSchema()
        ->getFormFormatter()
        ->getFieldErrorClass()
      ;

      $validClass = $this
        ->getWidgetSchema()
        ->getFormFormatter()
        ->getFieldValidClass()
      ;

      foreach ($this as $field)
      {
        $this->_recursiveFormFieldClases($field, $errorClass, $validClass);
      }
    }
  }

  /**
   * Recursive method to add classes to form fields even when they're deep
   *
   * @param   sfFormField $field
   * @param   string $errorClass
   * @param   string $validClass
   * @return  void
   */
  protected function _recursiveFormFieldClases($field, $errorClass, $validClass)
  {
    // recursive form field schemas;
    if ($field instanceof sfFormFieldSchema)
    {
      foreach ($field as $f)
      {
        $this->_recursiveFormFieldClases($f, $errorClass, $validClass);
      }
      return;
    }

    if ($field->hasError())
    {
      $field->getWidget()->setAttribute(
        'class',
        $field->getWidget()->getAttribute('class')
          ? $field->getWidget()->getAttribute('class') . ' ' . $errorClass
          : $errorClass
      );
    }
    else if (
      $this->getUseFieldValidClassServerSide()
      &&
      ($this->getUseValidClassOnEmptyFields () || $field->getValue())
    )
    {
      $field->getWidget()->setAttribute(
        'class',
        $field->getWidget()->getAttribute('class')
          ? $field->getWidget()->getAttribute('class') . ' ' . $validClass
          : $validClass
      );      
    }
  }

  /**
   * @todo
   * @return  string
   */
  public function getValidationScriptPath()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('url');

    $this->rewind();

    return url_for(
      '@'
      . sfConfig::get(
        'app_sfJqueryValidationPlugin_route', 'sfJqueryValidation'
      )
      . '?form=' . get_class($this)
      . '&name_format=' . $this->getWidgetSchema()->getNameFormat()
      . '&id_format=' . $this->getWidgetSchema()->getIdFormat()
      . (sfConfig::get('app_sfJqueryValidationPlugin_asset_version')
        ? '&asset-version=' . sfConfig::get('app_sfJqueryValidationPlugin_asset_version')
        : ''
      )
    );
  }


  public function getJavaScripts()
  {
    if (!$this->getUseJqueryValidation())
    {
      return parent::getJavaScripts();
    }

    return array_merge(
      parent::getJavaScripts(),
      array($this->getValidationScriptPath()),
      $this->getJqueryValidationGenerator()->getJavascripts()
    );
  }

  public function getStylesheets()
  {
    if (!$this->getUseJqueryValidation())
    {
      return parent::getStylesheets();
    }

    return array_merge(
      parent::getStylesheets(),
      $this->getJqueryValidationGenerator()->getStylesheets()
    );
  }

  /**
   * @return  string
   */
  public function getUseJqueryValidation()
  {
    return $this->useJqueryValidation;
  }

  /**
   * @param   string  $useJqueryValidation
   * @return  self
   */
  public function setUseJqueryValidation($useJqueryValidation)
  {
    $this->useJqueryValidation = $useJqueryValidation;
    return $this;
  }

  /**
   * @return  sfJqueryValidation|null
   */
  public function getJqueryValidationGenerator()
  {
    return $this->jqueryValidationGenerator;
  }

  /**
   * @param   sfJqueryValidationGenerator $jqueryValidationGenerator
   * @return  self
   */
  public function setJqueryValidationGenerator(
    sfJqueryValidationGenerator $jqueryValidationGenerator
  )
  {
    $this->jqueryValidationGenerator = $jqueryValidationGenerator;
    return $this;
  }
}
