<?php
/**
 * sfJqueryValidationParsersfValidationUrl
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorUrl
  extends sfJqueryValidationParsersfValidatorRegex
{
  /**
   * @see   parent
   */
  protected function _generateRules()
  {
    parent::_generateRules();

    // because symfony uses a multi line regex we'll use the jquery validation
    // method instead of a hacky string replace

    $this->addRule('url', new sfJqueryValidationValidatorRule(
      'true',
      $this->getRule('regex')->getMessage(),
      sfJqueryValidationValidatorRule::STR_RAW,
      sfJqueryValidationValidatorRule::STR_RAW
    ));

    $this->removeRule('regex');
  }

  
}
