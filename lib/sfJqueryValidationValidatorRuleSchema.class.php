<?php
/**
 * sfJqueryValidationValidatorRuleSchema
 *
 * Stores a collection of sfJqueryValidationValidatorRule
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  ValidatorRuleSchema
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationValidatorRuleSchema 
  extends sfJqueryValidationValidatorRule
{

  /**
   * @var string
   */
  protected $_name;

  /**
   * @var sfFormField
   */
  protected $_field;

  /**
   * Collection of sfValidatorBase objects
   *
   * @var array
   */
  protected $_validators = array();


  /**
   * A collection of validation parsers
   *
   * @var array
   */
  protected $_parserCollection;

  /**
   * @param   string      $name
   * @param   sfFormField $field
   * @param   array       $validators
   *
   * @return  void
   */
  public function __construct(
    $name, sfFormField $field, array $validators)
  {
    $this
      ->setName($name)
      ->setField($field)
      ->setValidators($validators)
    ;
  }

  /**
   * @return  string
   */
  public function getName()
  {
    return $this->_name;
  }

  /**
   * @param   string  $name
   * @return  self
   */
  public function setName($name)
  {
    $this->_name = $name;
    return $this;
  }

  /**
   * @return  sfFormField
   */
  public function getField()
  {
    return $this->_field;
  }

  /**
   * @param   sfFormField   $field
   * @return  self
   */
  public function setField(sfFormField $field)
  {
    $this->_field = $field;
    return $this;
  }

  /**
   * @return  array
   */
  public function getValidators()
  {
    return $this->_validators;
  }

  /**
   * @param   array   $validators
   * @return  self
   */
  public function setValidators(array $validators)
  {
    $this->_validators = $validators;
    return $this;
  }

  /**
   * @see     parent
   * @param   string  $indent   How much to indent the output
   * @return  string
   */
  public function getRule($indent = '      ')
  {
    $ruleCollection = array();

    foreach ($this->_getParserCollection() as $parser)
    {
      $rules = array();

      foreach ($parser->getRulesByName($this->getName()) as $name => $rule)
      {
        $rules[$name] = $rule->getRule();
      }

      if ($rules)
      {
        $ruleCollection[] =
          sfJqueryValidationGenerator::generateJavascriptObject($rules, $indent)
        ;
      }
    }

    return '[' . implode(', ', $ruleCollection) . ']';
  }

  /**
   * @see     parent
   * @return  string
   */
  public function getMessage($indent = '      ')
  {
    if (parent::getMessage() !== null)
    {
      return parent::getMessage();
    }

    $messageCollection = array();

    foreach ($this->_getParserCollection() as $parser)
    {
      $rules = array();

      foreach ($parser->getRulesByName($this->getName()) as $name => $rule)
      {
        $rules[$name] = $rule->getMessage();
      }

      if ($rules)
      {
        $messageCollection[] =
          sfJqueryValidationGenerator::generateJavascriptObject($rules, $indent)
        ;
      }
    }

    $messagesJsObject = '[' . implode(', ', $messageCollection) . ']';

    return $this->_buildJsMessageParser($messagesJsObject);
    
  }

  /**
   * Get an array of parsers
   *
   * @param   bool    $rebuild
   * @return  array
   */
  protected function _getParserCollection($rebuild = false)
  {
    if ($this->_parserCollection === null || $rebuild)
    {
      $this->_parserCollection = array();

      foreach ($this->getValidators() as $validator) {
        $parserFactory = new sfJqueryValidationValidatorParserFactory(
          $this->getName(),
          $this->getField(),
          $validator
        );

        $this->_parserCollection[] = $parserFactory->getParser();
      }
    }

    return $this->_parserCollection;
  }

  protected function _buildJsMessageParser($messagesJsObject)
  {
    $backupMessage = "'Invalid.'";

    $javascript = <<<EOT
function (ruleParams, element) {
  return $.validator.sfJqueryValidationPlugin.parseMessagesFromElementData.call(
    this,
    ruleParams,
    element,
    $messagesJsObject,
    $backupMessage,
    'jqValError'
  );
}
EOT;

    return $javascript;


  }
}
