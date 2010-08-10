<?php
/**
 * jQuery validation extensions to the table widget form
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  formSchemaFormatter
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */
class sfWidgetFormSchemaFormatterTableJqueryValidation
  extends sfWidgetFormSchemaFormatterTable
  implements sfWidgetFormSchemaFormatterJqueryValidationInterface
{
  protected
    $errorRowFormat        = "<tr>%global_error%
                              <td colspan=\"2\">\n%errors%</td>\n
                              </tr>\n",
    $errorListFormatInARow = "<ul class=\"error_list sf_errors\">\n%errors%  </ul>\n",
    $requiredFormat        = '<span class="required">*</span>',
    $form                  = null,
    $markRequired          = true,
    $globalErrorFormat     = "<td colspan=\"2\" class=\"global_error\">\n%errors%</td>\n",
    $fieldErrorClass       = 'error',
    $fieldValidClass       = 'valid',
    $jqueryValidationErrorElement
                           = 'li',
    $jqueryValidationWrapper
                           = 'ul class="error_list jq_validation_errors"',
    $jqueryValidationErrorContainer
                          = "",
    $jqueryValidationSubmitHandlerCallback
                          = "",
    $jqueryValidationInvalidHandlerCallback
                          = "",
    $jqueryValidationErrorPlacementCallback
                          = "
      console.log(error);
      element.before(error);
    ",
    $jqueryValidationShowErrorsCallback
                          = "",
    $jqueryValidationHighlightCallback
                          = "
      // get rid of existing sf errors
      $(element).prevAll('.sf_errors').remove();
      $(element).removeClass(validClass).addClass(errorClass);
    ",
    $jqueryValidationUnhighlightCallback
                          = "
      // get rid of existing sf errors
      $(element).prevAll('.sf_errors').remove();
      $(element).removeClass(errorClass).addClass(validClass);
    "
  ;
 
  /**
   * @param   sfForm $form
   * @return  self
   */
  public function setForm(sfFormJqueryValidationInterface $form)
  {
    $this->form = $form;
    return $this;
  }

  /**
   * @return  sfForm|null
   */
  public function getForm()
  {
    return $this->form;
  }

  /**
   * @param   bool  $markRequired
   * @return  self
   */
  public function setMarkRequired($markRequired)
  {
    $this->markRequired = $markRequired;
    return $this;
  }

  /**
   * @return bool
   */
  public function getMarkRequired()
  {
    return $this->markRequired;
  }

  /**
   * @param   string  $requiredFormat
   * @return  self
   */
  public function setRequiredFormat($requiredFormat)
  {
    $this->requiredFormat = $requiredFormat;
    return $this;
  }

  /**
   * @return string
   */
  public function getRequiredFormat()
  {
    return $this->requiredFormat;
  }

  /**
   * If field is required append the required format to the label name
   * 
   * @see parent
   */
  public function generateLabelName($name)
  {    
    $label = parent::generateLabelName($name);

    $validatorSchema = $this->getForm()
      ? $this->getForm()->getValidatorSchema()
      : null
    ;

    if (
      !$this->getMarkRequired()
      ||
      !$validatorSchema
      ||
      !isset($validatorSchema[$name])
    )
    {
      return $label;
    }

    if (
      $validatorSchema[$name]->hasOption('required')
      &&
      $validatorSchema[$name]->getOption('required')
      // dont mark a schema as required as it's got it's own fields
      &&
      !($validatorSchema[$name] instanceof sfValidatorSchema)
    )
    {
      $label .= $this->requiredFormat;
    }

    return $label;

  }

  /**
   * Overloading this method as a way to strip required from labels
   *
   * Bit of a hack!
   *
   * @param   mixed   $errors
   * @return  string
   */
  public function formatErrorsForRow($errors)
  {
    if (is_array($errors) && $this->getMarkRequired())
    {
      foreach ($errors as $name => $error)
      {
        $fixedName = str_replace($this->requiredFormat, '', $name);

        if ($fixedName != $name)
        {
          $errors[$fixedName] = $error;
          unset($errors[$name]);
        }
      }
    }

    return parent::formatErrorsForRow($errors);
  }

  /**
   * Formats error row, spits out just an error at the top if theres any errors
   * in the form
   *
   * @see     parent
   * @param   array   $errors
   * @return  string
   */
  public function formatErrorRow($errors)
  {
    $globalErrorMessage = $this->getForm()
      ? $this->getForm()->getGlobalErrorMessage()
      : null
    ;

    if (
      ($globalErrorMessage && $this->getForm()->hasErrors())
      ||
      $errors
    )
    {
      return strtr(
        $this->getErrorRowFormat(),
        array(
          '%global_error%' => $this->formatGlobalError($globalErrorMessage),
          '%errors%' => $this->formatErrorsForRow($errors)
        )
      );
    }

    return '';

  }

  public function setGlobalErrorFormat($globalErrorFormat)
  {
    $this->globalErrorFormat = $globalErrorFormat;
    return $this;
  }

  public function getGlobalErrorFormat()
  {
    return $this->globalErrorFormat;
  }

  public function formatGlobalError($message)
  {
    return strtr($message, array('%error%' => $message));
  }

  public function setFieldErrorClass($fieldErrorClass)
  {
    $this->fieldErrorClass = $fieldErrorClass;
    return $this;
  }

  public function getFieldErrorClass()
  {
    return $this->fieldErrorClass;
  }

  public function setFieldValidClass($fieldValidClass)
  {
    $this->fieldValidClass = $fieldValidClass;
    return $this;
  }

  public function getFieldValidClass()
  {
    return $this->fieldValidClass;
  }

  public function setJqueryValidationErrorElement($jqueryValidationErrorElement)
  {
    $this->jqueryValidationErrorElement = $jqueryValidationErrorElement;
    return $this;
  }

  public function getJqueryValidationErrorElement()
  {
    return $this->jqueryValidationErrorElement;
  }

  public function setJqueryValidationWrapper($jqueryValidationWrapper)
  {
    $this->jqueryValidationWrapper = $jqueryValidationWrapper;
    return $this;
  }

  public function getJqueryValidationWrapper()
  {
    return $this->jqueryValidationWrapper;
  }

  public function setJqueryValidationErrorContainer(
    $jqueryValidationErrorContainer
  )
  {
    $this->jqueryValidationErrorContainer = $jqueryValidationErrorContainer;
    return $this;
  }

  public function getJqueryValidationErrorContainer()
  {
    return $this->jqueryValidationErrorContainer;
  }

  public function getJqueryValidationSubmitHandlerCallback()
  {
    return $this->jqueryValidationSubmitHandlerCallback;
  }

  public function setJqueryValidationSubmitHandlerCallback(
    $jqueryValidationSubmitHandlerCallback
  )
  {
    $this->jqueryValidationSubmitHandlerCallback
      = $jqueryValidationSubmitHandlerCallback;
    return $this;
  }

  public function getJqueryValidationInvalidHandlerCallback()
  {
    return $this->jqueryValidationInvalidHandlerCallback;
  }

  public function setJqueryValidationInvalidHandlerCallback(
    $jqueryValidationInvalidHandlerCallback
  )
  {
    $this->jqueryValidationInvalidHandlerCallback
      = $jqueryValidationInvalidHandlerCallback;
    return $this;
  }

  public function getJqueryValidationShowErrorsCallback()
  {
    return $this->jqueryValidationShowErrorsCallback;
  }

  public function setJqueryValidationShowErrorsCallback(
    $jqueryValidationShowErrorsCallback
  )
  {
    $this->jqueryValidationShowErrorsCallback
      = $jqueryValidationShowErrorsCallback;
    return $this;
  }

  public function getJqueryValidationErrorPlacementCallback()
  {
    return $this->jqueryValidationErrorPlacementCallback;
  }

  public function setJqueryValidationErrorPlacementCallback(
    $jqueryValidationErrorPlacementCallback
  )
  {
    $this->jqueryValidationErrorPlacementCallback
      = $jqueryValidationErrorPlacementCallback;
    return $this;
  }

  public function getJqueryValidationHighlightCallback()
  {
    return $this->jqueryValidationHighlightCallback;
  }

  public function setJqueryValidationHighlightCallback(
    $jqueryValidationHighlightCallback
  )
  {
    $this->jqueryValidationHighlightCallback
      = $jqueryValidationHighlightCallback;
    return $this;
  }

  public function getJqueryValidationUnhighlightCallback()
  {
    return $this->jqueryValidationUnhighlightCallback;
  }

  public function setJqueryValidationUnhighlightCallback(
    $jqueryValidationUnhighlightCallback
  )
  {
    $this->jqueryValidationUnhighlightCallback
      = $jqueryValidationUnhighlightCallback;
    return $this;
  }
}