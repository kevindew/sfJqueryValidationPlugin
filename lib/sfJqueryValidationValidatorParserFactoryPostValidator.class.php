<?php
/**
 * sfJqueryValidationValidatorParserFactoryPostValidator
 *
 * Takes a field and a validator and pops out a parser for the validator
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  Post Validator Parser Factory
 * @author      Kevin Dew  <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationValidatorParserFactoryPostValidator
extends sfJqueryValidationValidatorParserFactoryPrePostValidator
{
  /**
   * An array of prefixs that will be used to find the parser class
   *
   * @var array
   */
  protected static $_parserClassPrefixes = array(
    'sfJqueryValidationParserPostValidator'
  );

  /**
   * @see     self::$_parserClassPrefixes
   * @param   array $parserClassPrefixes
   * @return  void
   */
  public static function setParserClassPrefixes(array $parserClassPrefixes)
  {
    self::$_parserClassPrefixes = $parserClassPrefixes;
  }

  /**
   * @see     self::$_parserClassPrefixes
   * @return  array
   */
  public static function getParserClassPrefixes()
  {
    return self::$_parserClassPrefixes;
  }
}
