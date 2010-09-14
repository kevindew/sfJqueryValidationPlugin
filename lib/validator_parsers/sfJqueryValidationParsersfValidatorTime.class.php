<?php
/**
 * sfJqueryValidationParsersfValidationTime
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorTime
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
    if (
      $this->getField()->getWidget() instanceof sfWidgetFormTime
    )
    {
      return $this->_generatesfWidgetFormTimeRules();
    }
    else
    {
      return $this->_generateGenericWidgetRules();
    }
  }

  /**
   * Generates the rules for a sfWidgetFormTime
   *
   * @return  void
   */
  protected function _generatesfWidgetFormTimeRules()
  {
    $widget = $this->getField()->getWidget();

    if (!$widget instanceof sfWidgetFormTime)
    {
      throw new Exception('Widget must be an instance of sfWidgetFormTime');
    }

    $validatorRules = array();

    // handle required
    if (
      $this->getValidator()->hasOption('required')
      &&
      $this->getValidator()->getOption('required')
    )
    {
      $validatorRules['required'] = new sfJqueryValidationValidatorRule(
        'true',
        $this->getValidator()->getMessage('required')
      );
    }

    $validatorRules['validArrayTime'] = new sfJqueryValidationValidatorRule(
      'true',
      $this->getValidator()->getMessage('invalid')
    );

    $groups = array();

    // types to use
    $types = array(
      'hour', 'minute', 'second'
    );

    // set groups and validation rules
    foreach ($types as $type)
    {
      $fieldName = $this->getName() . '[' . $type . ']';

      $groups[] = $fieldName;

      $this->setRulesByName($validatorRules, $fieldName);
    }

    $this->setGroup($this->getName(), $groups);

  }

  /**
   * Generates generic rules for a time (expecting a single field like a input
   * text)
   *
   * @return  void
   */
  protected function _generateGenericWidgetRules()
  {
    parent::_generateRules();

    if ($this->getValidator()->getOption('time_format'))
    {
      $timeFormatError = $this->getValidator()->getOption('time_format_error')
        ? $this->getValidator()->getOption('time_format_error')
        : $this->getValidator()->getOption('time_format')
      ;

      $this->addRule(
        'regex',
        new sfJqueryValidationValidatorRule(
          $this->getValidator()->getOption('time_format'),
          $this->generateMessageJsFunctionReplace(
            $this->getValidator()->getMessage('bad_format'),
            array(
              '%value%' =>
                sfJqueryValidationParsersfValidatorBase::PRINT_JQUERY_VALUE,
              '%time_format%' => $timeFormatError
            )
          ),
          sfJqueryValidationValidatorRule::STR_RAW,
          sfJqueryValidationValidatorRule::STR_RAW
        )
      );
    }

  }
}
