<?php
/**
 * sfJqueryValidationValidatorRule
 *
 * Stores a validation rule, message and group
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  ValidatorRule
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationValidatorRule
{
  /**
   * If a rule/message string doesnt need quotes
   */
  const STR_RAW = 0;

  /**
   * If a rule/message string needs quotes
   */
  const STR_QUOTE = 1;

  /**
   * @var string
   */
  protected $_rule;

  /**
   * @var string
   */
  protected $_message;

  /**
   * @param   string  $rule
   * @param   string  $message
   * @param   int     $ruleType     (Optional) By default adds quote to rule
   * @param   int     $messageType  (Optional) by default adds quote to message
   */
  public function __construct(
    $rule,
    $message,
    $ruleType = self::STR_RAW,
    $messageType = self::STR_QUOTE
  )
  {
    $this
      ->setRule($rule, $ruleType)
      ->setMessage($message, $messageType)
    ;
  }

  /**
   * @param   string  $rule
   * @param   int     $type   (Optional) By default adds quote to rule
   * @return  self
   */
  public function setRule($rule, $type = self::STR_RAW)
  {
    $this->_rule = $this->_setString($rule, $type);
    return $this;
  }

  /**
   * @return  string
   */
  public function getRule()
  {
    return $this->_rule;
  }

  /**
   * @param   string  $message
   * @param   int     $type   (Optional) By default adds quote to message
   * @return  self
   */
  public function setMessage($message, $type = self::STR_QUOTE)
  {
    $this->_message = $this->_setString($message, $type);
    return $this;
  }

  /**
   * @return  string
   */
  public function getMessage()
  {
    return $this->_message;
  }

  /**
   * Formats the string
   *
   * @param   string  $str
   * @param   int     $type
   * @return  string
   */
  protected function _setString($str, $type = self::STR_QUOTE)
  {
    return $type == self::STR_QUOTE
      ? '"' . addcslashes($str, '"') . '"'
      : $str
    ;
  }
}
