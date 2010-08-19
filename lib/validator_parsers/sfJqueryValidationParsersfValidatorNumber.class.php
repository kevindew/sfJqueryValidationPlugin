<?php
/**
 * sfJqueryValidationParsersfValidationNumber
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorNumber
  extends sfJqueryValidationParsersfValidatorBase
{
  /**
   * @see   parent
   */
  protected function _generateRules()
  {
    parent::_generateRules();

    // match int by regular experession
    $this->addRule(
      'number',
      new sfJqueryValidationValidatorRule(
        'true',
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

    if ($this->getValidator()->hasOption('max'))
    {

      $this->addRule(
        'max',
        new sfJqueryValidationValidatorRule(
          $this->getValidator()->getOption('max'),
          $this->generateMessageJsFunctionReplace(
            $this->getValidator()->getMessage('max'),
            array(
              '%value%' => sfJqueryValidationParsersfValidatorBase::PRINT_JQUERY_VALUE,
              '%max%' => 'ruleParams'
            )
          ),
          sfJqueryValidationValidatorRule::STR_RAW,
          sfJqueryValidationValidatorRule::STR_RAW
        )
      );
    }

    if ($this->getValidator()->hasOption('min'))
    {

      $this->addRule(
        'min',
        new sfJqueryValidationValidatorRule(
          $this->getValidator()->getOption('min'),
          $this->generateMessageJsFunctionReplace(
            $this->getValidator()->getMessage('min'),
            array(
              '%value%' => sfJqueryValidationParsersfValidatorBase::PRINT_JQUERY_VALUE,
              '%min%' => 'ruleParams'
            )
          ),
          sfJqueryValidationValidatorRule::STR_RAW,
          sfJqueryValidationValidatorRule::STR_RAW
        )
      );
    }
  }
}
