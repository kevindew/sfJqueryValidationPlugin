<?php
/**
 * sfJqueryValidationParsersfValidationInteger
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorInteger
  extends sfJqueryValidationParsersfValidatorNumber
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

    $this->removeRule('number');


    // match int by regular experession
    $this->addRule(
      'regex',
      new sfJqueryValidationValidatorRule(
        '/^-?[0-9]+$/',
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
