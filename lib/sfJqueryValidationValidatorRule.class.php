<?php

class sfJqueryValidationValidatorRule
{
  const STR_RAW = 0;
  const STR_QUOTE = 1;

  protected $_rule;

  protected $_message;

  public function __construct(
    $rule,
    $message,
    $ruleType = self::STR_QUOTE,
    $messageType = self::STR_QUOTE
  )
  {
    $this
      ->setRule($rule, $ruleType)
      ->setMessage($message, $messageType)
    ;
  }

  public function setRule($rule, $type = self::STR_QUOTE)
  {
    $this->_rule = $this->_setString($rule, $type);
    return $this;
  }

  public function getRule()
  {
    return $this->_rule;
  }

  public function setMessage($message, $type = self::STR_QUOTE)
  {
    $this->_message = $this->_setString($message, $type);
    return $this;
  }

  public function getMessage()
  {
    return $this->_message;
  }

  protected function _setString($str, $type = self::STR_QUOTE)
  {
    return $type == self::STR_QUOTE
      ? '"' . $str . '"'
      : $str
    ;
  }
}
