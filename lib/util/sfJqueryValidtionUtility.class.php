<?php
/**
 * Utility class of static methods for sfJqueryValdiationPlugin
 *
 * Some methods are based on code by Alexandre Mogère in sfCombine
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  sfJqueryValidationUtility
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */
class sfJqueryValidationUtility
{
  /**
   * Send GZip headers if possible
   *
   * @author  Alexandre Mogère
   * @return  void
   */
  static public function setGzip()
  {
    // gzip compression
    if (
      sfConfig::get('app_sfJqueryValidationPlugin_gzip', true)
      &&
      !self::_checkGzipFail()
    )
    {
      ob_start("ob_gzhandler");
    }
  }

  /**
   * Send cache headers if possible.
   *
   * @author  Alexandre Mogère
   * @param   sfResponse $response
   * @return  void
   */
  static public function setCacheHeaders($response)
  {

    $max_age = sfConfig::get(
      'app_sfJqueryValidationPlugin_client_cache_max_age', false
    );

    if ($max_age !== false)
    {
      $lifetime = $max_age * 86400; // 24*60*60
      $response->addCacheControlHttpHeader('max-age', $lifetime);
      $response->setHttpHeader(
        'Pragma',
        sfConfig::get('app_sfJqueryValidationPlugin_pragma_header', 'public')
      );
      $response->setHttpHeader(
        'Expires', $response->getDate(time() + $lifetime)
      );
    }
  }

  /**
   * Check whether we can send gzip
   * 
   * @author  Alexandre Mogère
   * @return  bool
   */
  static protected function _checkGzipFail()
  {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($userAgent, 'Mozilla/4.0 (compatible; MSIE ') !== 0 
    || strpos($userAgent, 'Opera') !== false) {
      return false;
    }
    
    $version = floatval(substr($userAgent, 30));

    return $version < 6
      || ($version == 6 && strpos($userAgent, 'SV1') === false);
  }

  static public function minify($javascript)
  {
    if (
      sfConfig::get('app_sfJqueryValidationPlugin_minify')
      &&
      sfConfig::get('app_sfJqueryValidationPlugin_minify_method')
      &&
      is_callable(sfConfig::get('app_sfJqueryValidationPlugin_minify_method'))
    )
    {
      $javascript = call_user_func(
        sfConfig::get('app_sfJqueryValidationPlugin_minify_method'),
        $javascript,
        sfConfig::get(
          'app_sfJqueryValidationPlugin_minify_method_options',
          array()
        )
      );
    }

    return $javascript;
  }
}
