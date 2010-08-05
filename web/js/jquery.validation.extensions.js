/**
 * Extensions to jquery.validation
 *
 * Based on the reg-exp extension by James Beattie for
 * sfJqueryFormValidationPlugin
 *
 * @package     sfJqueryValidationPlugin
 * @subpackage  jquery.validation
 * @author      Kevin Dew <kev@dewsolutions.co.uk>
 * @author      James Beattie
 * @version     SVN: $Id$
 */

(function($) {
  $.validator.addMethod(
      'regex',
      function(value, element, regex) {
        return this.optional(element) || regex.test(value);
      },
      'Invalid.'
  );
})(jQuery);