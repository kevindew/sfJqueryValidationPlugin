<?php
/**
 * sfJqueryValidationParsersfValidationBoolean
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorBoolean
  extends sfJqueryValidationParsersfValidatorBase
{
  
  /**
   * @see   parent
   */
  protected function _generateRules()
  {
    parent::_generateRules();

    $range = array_merge(
      $this->getValidator()->getOption('true_values'),
      $this->getValidator()->getOption('false_values')
    );


    $this->addRule(
      'range',
      new sfJqueryValidationValidatorRule(
        $this->buildRange($range),
        $this->generateMessageJsFunctionReplace(
          $this->getValidator()->getMessage('invalid'),
          array(
            '%value%' => sfJqueryValidationParsersfValidatorBase::PRINT_JQUERY_VALUE,
          )
        ),
        sfJqueryValidationValidatorRule::STR_RAW,
        sfJqueryValidationValidatorRule::STR_RAW
      )
    );
  }
}
