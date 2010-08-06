<?php
/**
 * sfJqueryValidationParsersfValidationString
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorString
  extends sfJqueryValidationParsersfValidatorBase
{
  /**
   * @see   parent
   */
  protected function _generateRules()
  {
    parent::_generateRules();

    if ($this->getValidator()->hasOption('max_length'))
    {
      $this->addRule(
        'maxlength',
        new sfJqueryValidationValidatorRule(
           $this->getValidator()->getOption('max_length'),
           $this->getValidator()->getMessage('max_length')
        )
      );
    }

    if ($this->getValidator()->hasOption('min_length'))
    {
      $this->addRule(
        'minlength',
        new sfJqueryValidationValidatorRule(
           $this->getValidator()->getOption('min_length'),
           $this->getValidator()->getMessage('min_length')
        )
      );
    }
  }
}
