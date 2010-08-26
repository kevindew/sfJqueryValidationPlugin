<?php
/**
 * sfJqueryValidationParsersfValidationAnd
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorAnd
  extends sfJqueryValidationParsersfValidatorBase
{

  /**
   * @see   parent
   */
  public function configure()
  {
    parent::configure();

    $this->setJavascripts(array_merge(
       $this->getJavascripts(),
       array('/sfJqueryValidationPlugin/js/jquery.validation.extensions.js')
    ));
  }

  /**
   * @see   parent
   */
  protected function _generateRules()
  {
    parent::_generateRules();

    $ruleSchema = new sfJqueryValidationValidatorRuleSchema(
      $this->getName(),
      $this->getField(),
      $this->getValidator()->getValidators()
    );

    if ($this->getValidator()->getMessage('invalid'))
    {
      $ruleSchema->setMessage(
        $this->generateMessageJsFunctionReplace(
          $this->getValidator()->getMessage('invalid'),
          array(
            '%value%' => sfJqueryValidationParsersfValidatorBase::PRINT_JQUERY_VALUE,
          )
        ),
        sfJqueryValidationValidatorRule::STR_RAW
      );
    }

    $this->addRule('and', $ruleSchema);

  }

}
