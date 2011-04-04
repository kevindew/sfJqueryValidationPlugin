<?php

/**
 * A form class that extends the sfJqueryValidationPlugin with some common form
 * additions
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  form
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */
class sfFormJqueryValidationFormExtended
  extends sfFormJqueryValidation
{
  /**
   * Change the csrf errors to be a bit more friendly
   *
   * @see   parent
   */
  public function addCSRFProtection($secret = null)
  {
    parent::addCSRFProtection($secret);

    if ($this->isCSRFProtected())
    {
      $this->getWidget(self::$CSRFFieldName)->setLabel('Session Error');

      $this->getValidator(self::$CSRFFieldName)->setMessage(
        'csrf_attack',
        'This session has expired. Please try again and ensure cookies are enabled'
      );
    }

  }

  /**
   * Method to ensure form CSRF protection doesn't happen again and again
   *
   * @todo  FIXME!
   *
   * @see   parent
   */
  protected function doBind(array $values)
  {
    $this->_resetCSRFProtection();
    parent::doBind($values);

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
   * Format the global errors for the form
   *
   * Uses the formatErrorRow method on the form formatter rather than
   * formatErrorsForRow which is what it uses by default and is inconsistent
   * with echoing just a form
   *
   * @return string
   */
  public function renderGlobalErrors()
  {
    return $this->widgetSchema->getFormFormatter()->formatErrorRow(
      $this->getGlobalErrors()
    );
  }
}
