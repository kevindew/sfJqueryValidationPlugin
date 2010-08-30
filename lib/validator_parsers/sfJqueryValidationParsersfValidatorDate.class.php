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
    if ($this->getField()->getWidget() instanceof sfWidgetFormDate)
    {
      return $this->_generatesfWidgetFormDateRules();
    }
    else
    {

    }
  }

  protected function _generatesfWidgetFormDateRules()
  {
    $widget = $this->getField()->getWidget();

    if (!$widget instanceof sfWidgetFormDate)
    {
      throw new Exception('Widget must be an instance of sfWidgetFormDate');
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
      $validatorRules['requiredArrayDate'] = new sfJqueryValidationValidatorRule(
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
      'month', 'day', 'year'
    );

    if ($this->getValidator()->getOption('with_time'))
    {
      $types = array_merge($types, array(
         'hour', 'minute', 'second'
      ));
    }


    // set groups and validation rules
    foreach ($types as $type)
    {
      $fieldName = $this->getName() . '[' . $type . ']';

      $groups[] = $fieldName;

      //$this->setRulesByName($validatorRules, $fieldName);
    }

    //$this->setGroup($this->getName(), $groups);

  }
}
