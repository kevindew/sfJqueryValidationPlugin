<?php
/**
 * sfJqueryValidationParsersfValidationDate
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Parser
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationParsersfValidatorDate
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
      $this->getField()->getWidget() instanceof sfWidgetFormDate
      ||
      $this->getField()->getWidget() instanceof sfWidgetFormDateTime
    )
    {
      return $this->_generatesfWidgetFormDateRules();
    }
    else
    {
      return $this->_generateGenericWidgetRules();
    }
  }

  /**
   * Generates the rules for a sfWidgetFormDate or a sfWidgetFormDateTime
   *
   * @return  void
   */
  protected function _generatesfWidgetFormDateRules()
  {
    $widget = $this->getField()->getWidget();

    if (
      !$widget instanceof sfWidgetFormDate
      &&
      !$widget instanceof sfWidgetFormDateTime
    )
    {
      throw new Exception(
        'Widget must be an instance of sfWidgetFormDate or sfWidgetFormDateTime'
      );
    }

    $validatorRules = array();

    $validatorRules['validArrayDate'] = new sfJqueryValidationValidatorRule(
      'true',
      $this->getValidator()->getMessage('invalid')
    );

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

    // max date
    if ($this->getValidator()->getOption('max'))
    {
      $max = $this->getValidator()->getOption('max');

      if (is_numeric($max))
      {
        $errorMessage = strtr($this->getValidator()->getMessage('max'), array(
          '%max%' =>  date(
            $this->getValidator()->getOption('date_format_range_error'),
            $max
          )
        ));

        $validatorRules['maxArrayDate'] = new sfJqueryValidationValidatorRule(
          $max, $errorMessage
        );
      }
      else
      {
        $dateMax = new DateTime($max);

        $errorMessage = strtr($this->getValidator()->getMessage('max'), array(
          '%max%' =>  $dateMax->format(
            $this->getValidator()->getOption('date_format_range_error')
          )
        ));

        $validatorRules['maxArrayDate'] = new sfJqueryValidationValidatorRule(
          $dateMax->format('U'), $errorMessage
        );
      }
    }

    // min date
    if ($this->getValidator()->getOption('min'))
    {
      $min = $this->getValidator()->getOption('min');

      if (is_numeric($min))
      {
        $errorMessage = strtr($this->getValidator()->getMessage('min'), array(
          '%min%' =>  date(
            $this->getValidator()->getOption('date_format_range_error'),
            $min
          )
        ));

        $validatorRules['minArrayDate'] = new sfJqueryValidationValidatorRule(
          $min, $errorMessage
        );
      }
      else
      {
        $dateMin = new DateTime($min);

        $errorMessage = strtr($this->getValidator()->getMessage('min'), array(
          '%min%' =>  $dateMin->format(
            $this->getValidator()->getOption('date_format_range_error')
          )
        ));

        $validatorRules['minArrayDate'] = new sfJqueryValidationValidatorRule(
          $dateMin->format('U'), $errorMessage
        );
      }
    }

    $groups = array();

    // types to use
    $types = array(
      'month', 'day', 'year', 'hour', 'minute', 'second'
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
   * Generates generic rules for a date (expecting a single field like a input
   * text)
   *
   * @return  void
   */
  protected function _generateGenericWidgetRules()
  {
    parent::_generateRules();
    $this->addRule('date', new sfJqueryValidationValidatorRule(
      'true', $this->getValidator()->getMessage('invalid')
    ));

    // max date
    if ($this->getValidator()->getOption('max'))
    {
      $max = $this->getValidator()->getOption('max');

      if (is_numeric($max))
      {
        $errorMessage = strtr($this->getValidator()->getMessage('max'), array(
          '%max%' =>  date(
            $this->getValidator()->getOption('date_format_range_error'),
            $max
          )
        ));

        $this->addRule('maxDate', new sfJqueryValidationValidatorRule(
          $max, $errorMessage
        ));
      }
      else
      {
        $dateMax = new DateTime($max);

        $errorMessage = strtr($this->getValidator()->getMessage('max'), array(
          '%max%' =>  $dateMax->format(
            $this->getValidator()->getOption('date_format_range_error')
          )
        ));

        $this->addRule('maxDate', new sfJqueryValidationValidatorRule(
          $dateMax->format('U'), $errorMessage
        ));
      }
    }

    // min date
    if ($this->getValidator()->getOption('min'))
    {
      $min = $this->getValidator()->getOption('min');

      if (is_numeric($min))
      {
        $errorMessage = strtr($this->getValidator()->getMessage('min'), array(
          '%min%' =>  date(
            $this->getValidator()->getOption('date_format_range_error'),
            $min
          )
        ));

        $this->addRule('minDate', new sfJqueryValidationValidatorRule(
          $min, $errorMessage
        ));
      }
      else
      {
        $dateMin = new DateTime($min);

        $errorMessage = strtr($this->getValidator()->getMessage('min'), array(
          '%min%' =>  $dateMin->format(
            $this->getValidator()->getOption('date_format_range_error')
          )
        ));

        $this->addRule('maxDate', new sfJqueryValidationValidatorRule(
          $dateMin->format('U'), $errorMessage
        ));
      }
    }
  }
}
