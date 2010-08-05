<?php

/**
 * A Form class designed to be integrated with jQuery Validation
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  formSchemaFormatter
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
    // path to jquery validation script
    $jqueryValidationScriptPath =
      'http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js'
    ,
    $useJqueryValidation = true
  ;


  /**
   * Handles setting up widget schema for required fields asterisks
   * Done here to ensure this is called
   * 
   * @see parent
   */
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    parent::__construct($defaults, $options, $CSRFSecret);

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

    // set validator script
    if (sfConfig::get('app_sfJqueryValidationPlugin_jquery_validation_script'))
    {
      $this->setJqueryValidationScriptPath(
        sfConfig::get('app_sfJqueryValidationPlugin_jquery_validation_script')
      );
    }

    $this->setUseJqueryValidation(
      sfConfig::get('app_sfJqueryValidationPlugin_jquery_validation_by_default')
    );

    $this->setJqueryValidationGenerator(new sfJqueryValidationGenerator($this));
    
  }

  /**
   * Change the csrf errors to be a bit more friendly
   *
   * @see   parent
   */
  public function addCSRFProtection($secret = null)
  {
    parent::addCSRFProtection($secret);

    $this->getWidget(self::$CSRFFieldName)->setLabel('Session Error');

    $this->getValidator(self::$CSRFFieldName)->setMessage(
      'csrf_attack',
      'This session has expired. Please try again and ensure cookies are enabled'
    );

  }

  /**
   * Method to ensure form CSRF protection doesn't happen again and again
   * 
   * @see parent
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);

    $this->_resetCSRFProtection();

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
   * Method to stop CSRF protection happening over and over again until a user
   * refreshes the page
   *
   * @return  void
   */
  protected function _resetCSRFProtection()
  {
    if ($this->isCSRFProtected())
    {
      $this->taintedValues[self::$CSRFFieldName] = $this->getCSRFToken();
    }
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
   * @return  string
   */
  public function getJqueryValidationScriptPath()
  {
    return $this->jqueryValidationScriptPath;
  }

  /**
   * @param   string  $jqueryValidationScriptPath
   * @return  self
   */
  public function setJqueryValidationScriptPath($jqueryValidationScriptPath)
  {
    $this->jqueryValidationScriptPath = $jqueryValidationScriptPath;
    return $this;
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

    return array_merge(parent::getJavaScripts(), array(
        $this->getJqueryValidationScriptPath(),
        $this->getValidationScriptPath()
    ));
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
