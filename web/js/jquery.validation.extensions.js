/**
 * Extensions to jquery.validation
 *
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  jquery.validation
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 */

(function($) {
  /**
   * Based on the reg-exp extension by James Beattie for
   * sfJqueryFormValidationPlugin
   */
  $.validator.addMethod(
      'regex',
      function(value, element, regex) {
        return this.optional(element) || regex.test(value);
      },
      'Invalid.'
  );
    
  /**
   * Based on the reg-exp extension by James Beattie for
   * sfJqueryFormValidationPlugin
   */
  $.validator.addMethod(
      'regexFail',
      function(value, element, regex) {
        return this.optional(element) || !regex.test(value);
      },
      'Invalid.'
  );
})(jQuery);