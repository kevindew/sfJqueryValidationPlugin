<?php
/**
 * sfJqueryValidationParsersfValidatorChoice
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorChoice
  extends sfJqueryValidationParsersfValidatorBase
{
  /**
   * Sets the validation rules, if its a multiple change name to have the []
   * suffix
   *
   * @see     parent
   * @return  void
   */
  protected function _generateRules()
  {
    $widget = $this->getField()->getWidget();

    // set the name for a multiple
    if ($widget->getOption('multiple'))
    {
      $this->setName($this->getName() . '[]');
    }

    parent::_generateRules();

    if ($this->getValidator()->hasOption('max'))
    {

      $this->addRule(
        'maxlength',
        new sfJqueryValidationValidatorRule(
          $this->getValidator()->getOption('max'),
          $this->generateMessageJsFunctionReplace(
            $this->getValidator()->getMessage('max'),
            array(
              '%max%' => 'ruleParams',
              '%count%' => 'this.getLength(jQuery(element).val(), element)'
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
        'minlength',
        new sfJqueryValidationValidatorRule(
          $this->getValidator()->getOption('min'),
          $this->generateMessageJsFunctionReplace(
            $this->getValidator()->getMessage('min'),
            array(
              '%min%' => 'ruleParams',
              '%count%' => 'this.getLength(jQuery(element).val(), element)'
            )
          ),
          sfJqueryValidationValidatorRule::STR_RAW,
          sfJqueryValidationValidatorRule::STR_RAW
        )
      );
    }
  }
  
}
