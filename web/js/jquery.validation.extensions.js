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


  /**
   * Method to validate an or validation type
   */
  $.validator.addMethod(
    'or',
    function(value, element, orArray) {

      // return true if length is 0
      if (orArray.length == 0) {
        return this.optional(element) || true;
      }

      var firstError = '';

      // assume we've failed we'll change this if we pass a test
      var pass = false;

      var methodThis = this;

      // use element data to store rules for error message
      if (typeof arguments[3] == 'undefined') {
        $(element).data('jqValError', []);
      }

      // loop through each validation object
      var count = 0;
      $.each(orArray, function(index, validatorObject) {

        var objectPass = true;

        // loop through each rule
        
        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(
            methodThis, value, element, arg, count
          )) {
            
            objectPass = false;

            // add or error data to the object so we can try work out error message
            if (!firstError) {
              $(element).data('jqValError').push(rule)
              $(element).data('jqValError').push(count);
              firstError = rule;
            }
            return false;
          }          

        });

        if (objectPass && !pass) {
          pass = true;
          return false;
        }
        count++;
      });

      return this.optional(element) || pass;
    },
    'Invalid.'
  );

  /**
   * Method to validate an and validation type
   */
  $.validator.addMethod(
    'and',
    function(value, element, andArray) {

      // return true if length is 0
      if (andArray.length == 0) {
        return this.optional(element) || true;
      }

      var allPassed = true;

      var firstError = '';

      var methodThis = this;

      // use element data to store rules for error message
      if (typeof arguments[3] == 'undefined') {
        $(element).data('jqValError', []);
      }

      // loop through each validation object
      var count = 0;
      $.each(andArray, function(index, validatorObject) {

        // loop through each rule

        $.each(validatorObject, function(rule, arg) {

          if (!$.validator.methods[rule].call(
            methodThis, value, element, arg, count
          )) {

            allPassed = false;

            // add and error data to the object so we can try work out error message
            if (!firstError) {
              $(element).data('jqValError').push(rule)
              $(element).data('jqValError').push(count);
              firstError = rule;
            }
            return false;
          }

        });

        if (!allPassed) {
          return false;
        }
        count++;
      });

      return this.optional(element) || allPassed;
    },
    'Invalid.'
  );

  /**
   * Validate if an array date is correct
   */
  $.validator.addMethod(
    'validArrayDate',
    function(value, element, valid) {

      return this.optional(element)
        || $.validator.sfJqueryValidationPlugin.buildArrayDate.call(this, element)
      ;

    },
    'Invalid.'
  );

  /**
   * Validate an array date to see if it meets a minimum
   */
  $.validator.addMethod(
    'minArrayDate',
    function(value, element, timestamp) {

			if (this.optional(element)) {
				return "dependency-mismatch";
      }

      try {
        var date = $.validator.sfJqueryValidationPlugin.buildArrayDate.call(
          this, element
        );

        if (date === false) {
          return false;
        }

        if (typeof date != 'object') {
          throw "Date not returned"
        }

        var checkDate = new Date(timestamp * 1000);

        return date >= checkDate


      } catch (error) {
        return "pending";
      }
    },
    'Please select a later date.'
  );

  /**
   * Validate an array date to see if it meets a maximum
   */
  $.validator.addMethod(
    'maxArrayDate',
    function(value, element, timestamp) {

			if (this.optional(element)) {
				return "dependency-mismatch";
      }

      try {
        var date = $.validator.sfJqueryValidationPlugin.buildArrayDate.call(
          this, element
        );

        if (date !== false && typeof date != 'object') {
          throw "Date not returned"
        }


        if (date === false) {
          return false;
        }

        var checkDate = new Date(timestamp * 1000);

        return date <= checkDate


      } catch (error) {
        return "pending";
      }
    },
    'Please select an earlier date.'
  );

  /**
   * Validate if an array time is correct
   */
  $.validator.addMethod(
    'validArrayTime',
    function(value, element, valid) {

      return this.optional(element)
        || $.validator.sfJqueryValidationPlugin.buildArrayTime.call(this, element)
      ;

    },
    'Invalid.'
  );

  /**
   * Check if a value is an array of choices
   */
  $.validator.addMethod(
    'choice',
    function(value, element, choices) {
      return this.optional(element) || ($.inArray(value, choices) >= 0)
    },
    'Required.'
  );

  /**
   * Validate a text date to see if it meets a minimum
   */
  $.validator.addMethod(
    'minDate',
    function(value, element, timestamp) {

			if (this.optional(element)) {
				return "dependency-mismatch";
      }

      try {

        return new Date(value) >= new Date(timestamp * 1000);


      } catch (error) {
        return "pending";
      }
    },
    'Please select a later date.'
  );

  /**
   * Validate a text date to see if it meets a maximum
   */
  $.validator.addMethod(
    'maxDate',
    function(value, element, timestamp) {

			if (this.optional(element)) {
				return "dependency-mismatch";
      }

      try {

        return new Date(value) <= new Date(timestamp * 1000);

      } catch (error) {
        return "pending";
      }
    },
    'Please select a later date.'
  );

  /**
   * Methods for the validator object itself
   */
  if (typeof $.validator.sfJqueryValidationPlugin == 'undefined') {
    $.validator.sfJqueryValidationPlugin = {};
  }

  /**
   * Method to return a message that is stored in an object based on data stored
   * in the element
   *
   * @param   mixed       rulesParams   The rule parameters for the validation
   *                                    rule
   * @param   DomElement  element
   * @param   Object      messages      An object of messages
   * @param   string      backupMessage A string backup message if parsing the
   *                                    messages object fails
   * @param   string      dataString    Where to check an element for the error
   *
   * @return  string
   */
  $.validator.sfJqueryValidationPlugin.parseMessagesFromElementData = function (
    ruleParams, element, messages, backupMessage, dataString
  ) {

    if (typeof $(element).data(dataString) !== 'object') {
      return backupMessage;
    }

    var jqValErrorArr = $(element).data(dataString);
    var first;

    while (typeof (first = jqValErrorArr.pop()) != 'undefined') {
      if (typeof messages[first] == 'undefined') {
        break;
      }

      messages = messages[first];

      if (typeof messages == 'function') {
        return messages.call(this, ruleParams, element);
      } else if (typeof messages == 'string') {
        var theregex = /\$?\{(\d+)\}/g;
        if (theregex.test(messages)) {
          messages = jQuery.format(messages.replace(theregex, '{$1}'), ruleParams);
        }
        return messages;
      }

    }

    return backupMessage;
  }

  $.validator.sfJqueryValidationPlugin.buildArrayDate = function (element) {

    var
      name = $(element).attr('name'),
      fieldNames = ['year', 'month', 'day', 'hour', 'minute', 'second'],
      form = $(element).parents('form').first(),
      fields = {},
      allBlank = true,
      allFilled = true,
      returnError = true,
      i
    ;

    $(element).data('checkedsfJqueryValidationDate', true);

    // need to strip the date part of the array off
    name = name.replace(/\[(year|month|day|hour|minute|second)\]/, '');

    // loop through the fields, put them into an object and do some preliminary
    // checks
    for (i = 0; i < fieldNames.length; i++) {
      fields[fieldNames[i]]
        = $('[name="' + name + '[' + fieldNames[i] + ']"]', form).first()
      ;

      // only check a field that exists
      if (fields[fieldNames[i]].length) {

        // set on change events
        fields[fieldNames[i]]
          .unbind(
            'keyup.jquery-validate-date-' + $(element).attr('id')
            + ' click.jquery-validate-date-' + $(element).attr('id')
          )
          .bind(
            'keyup.jquery-validate-date-' + $(element).attr('id')
            + ' click.jquery-validate-date-' + $(element).attr('id')
          , function() {
            $(element).valid();
          })
        ;

        var value = fields[fieldNames[i]].val();

        if (value !== '') {
          allBlank = false;
        }

        // we're assuming the second field isn't required
        if (fieldNames[i] != 'second') {
          if (value === '') {
            allFilled = false;
          }

          if (!(
            fields[fieldNames[i]].data('checkedsfJqueryValidationDate')
            &&
            (value === '' && !this.optional(fields[fieldNames[i]].get(0)))
          )) {
            returnError = false;
          }
        }
      }
    }

    // return pending because this condition requires other fields to change
    if (
      allBlank
      ||
      !allFilled && !returnError
    ) {
      return "pending";
    }

    if (!allFilled) {
      return false;
    }

    // build date

    // to work out a date we need atleast the month and the year
    try {

      if (
        !fields['year'].length
        ||
        !fields['month'].length
      ) {
          throw "Can't work out date without a year or month";
      }

      var year = fields['year'].val();
      var month = fields['month'].val();
      var day = fields['day'].length ? fields['day'].val() : 1;
      var hour = fields['hour'].length ? fields['hour'].val() : 0;
      var minute = fields['minute'].length ? fields['minute'].val() : 0;
      var second = fields['second'].length ? fields['second'].val() : 0;

      var date = new Date(year, month, day, hour, minute, second);

      return date;

    } catch (error) {
      return "pending";
    }
  }

  $.validator.sfJqueryValidationPlugin.buildArrayTime = function (element) {

    var
      name = $(element).attr('name'),
      fieldNames = ['hour', 'minute', 'second'],
      form = $(element).parents('form').first(),
      fields = {},
      allBlank = true,
      allFilled = true,
      returnError = true,
      i
    ;

    $(element).data('checkedsfJqueryValidationTime', true);

    // need to strip the date part of the array off
    name = name.replace(/\[(hour|minute|second)\]/, '');

    // loop through the fields, put them into an object and do some preliminary
    // checks
    for (i = 0; i < fieldNames.length; i++) {
      fields[fieldNames[i]]
        = $('[name="' + name + '[' + fieldNames[i] + ']"]', form).first()
      ;

      // only check a field that exists
      if (fields[fieldNames[i]].length) {

        // set on change events
        fields[fieldNames[i]]
          .unbind(
            'keyup.jquery-validate-time-' + $(element).attr('id')
            + ' click.jquery-validate-time-' + $(element).attr('id')
          )
          .bind(
            'keyup.jquery-validate-time-' + $(element).attr('id')
            + ' click.jquery-validate-time-' + $(element).attr('id')
          , function() {
            $(element).valid();
          })
        ;

        var value = fields[fieldNames[i]].val();

        if (value !== '') {
          allBlank = false;
        }

        // we're assuming the second field isn't required
        if (fieldNames[i] != 'second') {
          if (value === '') {
            allFilled = false;
          }

          if (!(
            fields[fieldNames[i]].data('checkedsfJqueryValidationTime')
            &&
            (value === '' && !this.optional(fields[fieldNames[i]].get(0)))
          )) {
            returnError = false;
          }
        }
      }
    }

    // return pending because this condition requires other fields to change
    if (
      allBlank
      ||
      !allFilled && !returnError
    ) {
      return "pending";
    }

    if (!allFilled) {
      return false;
    }

    return true
  }

})(jQuery);