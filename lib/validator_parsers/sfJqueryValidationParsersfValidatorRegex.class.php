<?php
/**
 * sfJqueryValidationParsersfValidationRegex
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorRegex
  extends sfJqueryValidationParsersfValidatorString
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

    $this->addRule(
      ($this->getValidator()->getOption('must_match') ? 'regex' : 'regexFail'),
      new sfJqueryValidationValidatorRule(
        $this->getValidator()->getPattern(),
        $this->generateMessageJsFunctionReplace(
          $this->getValidator()->getMessage('invalid'),
          array(
            '%value%' => 'jQuery(element).val()',
          )
        ),
        sfJqueryValidationValidatorRule::STR_RAW,
        sfJqueryValidationValidatorRule::STR_RAW
      )
    );
  }

  
}
