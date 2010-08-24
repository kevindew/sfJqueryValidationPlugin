<?php
/**
 * sfJqueryValidationParsersfValidationOr
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorOr
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

    $this->addRule('or', $ruleSchema);

  }

}
