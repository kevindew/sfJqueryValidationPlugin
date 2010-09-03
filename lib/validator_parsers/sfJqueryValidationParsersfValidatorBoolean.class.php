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

    $choices = array_merge(
      $this->getValidator()->getOption('true_values'),
      $this->getValidator()->getOption('false_values')
    );


    $this->addRule(
      'choice',
      new sfJqueryValidationValidatorRule(
        $this->buildChoices($choices),
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
