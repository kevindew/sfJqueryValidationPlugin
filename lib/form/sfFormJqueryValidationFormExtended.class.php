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
}
