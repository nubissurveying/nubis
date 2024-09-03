/*!
 * jQuery Validation Plugin 1.11.1
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-validation/
 * http://docs.jquery.com/Plugins/Validation
 *
 * Copyright 2013 Jörn Zaefferer
 * Released under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 */

(function() {

    function stripHtml(value) {
        // remove html tags and space chars
        return value.replace(/<.[^<>]*?>/g, ' ').replace(/&nbsp;|&#160;/gi, ' ')
                // remove punctuation
                .replace(/[.(),;:!?%#$'"_+=\/\-]*/g, '');
    }
    jQuery.validator.addMethod("maxWords", function(value, element, params) {

        // ignore if empty
        if (params == '') {
            return true;
        }
        //http://stackoverflow.com/questions/2315488/using-javascript-how-can-i-count-a-mix-of-asian-characters-and-english-words
        var r = new RegExp(
                '[A-Za-z0-9_\]+|' + // ASCII letters (no accents)
                '[\u3040-\u309F]+|' + // Hiragana
                '[\u30A0-\u30FF]+|' + // Katakana
                '[\u4E00-\u9FFF\uF900-\uFAFF\u3400-\u4DBF]', // Single CJK ideographs
                'g');

        var nwords = 0;
        var stripped = stripHtml(value);
        if (stripped) {
            var t = stripped.match(r);
            if (t) {
                nwords = t.length;
            }
        }
        return this.optional(element) || nwords <= params;


    }, jQuery.validator.format("Please enter {0} words or less."));

    jQuery.validator.addMethod("minWords", function(value, element, params) {

        // ignore if empty
        if (params == '') {
            return true;
        }

        //http://stackoverflow.com/questions/2315488/using-javascript-how-can-i-count-a-mix-of-asian-characters-and-english-words
        var r = new RegExp(
                '[A-Za-z0-9_\]+|' + // ASCII letters (no accents)
                '[\u3040-\u309F]+|' + // Hiragana
                '[\u30A0-\u30FF]+|' + // Katakana
                '[\u4E00-\u9FFF\uF900-\uFAFF\u3400-\u4DBF]', // Single CJK ideographs
                'g');

        var nwords = 0;
        var stripped = stripHtml(value);
        if (stripped) {
            var t = stripped.match(r);
            if (t) {
                nwords = t.length;
            }
        }
        return this.optional(element) || nwords >= params;
    }, jQuery.validator.format("Please enter at least {0} words."));

    jQuery.validator.addMethod("rangeWords", function(value, element, params) {

        // ignore if empty
        if (params.length == 0) {
            return true;
        }

        var valueStripped = stripHtml(value);
        var regex = /\b\w+\b/g;
        return this.optional(element) || valueStripped.match(regex).length >= params[0] && valueStripped.match(regex).length <= params[1];
    }, jQuery.validator.format("Please enter between {0} and {1} words."));

}());

jQuery.validator.addMethod("letterswithbasicpunc", function(value, element) {
    return this.optional(element) || /^[a-z\-.,()'"\s]+$/i.test(value);
}, "Letters or punctuation only please");

jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^\w+$/i.test(value);
}, "Letters, numbers, and underscores only please");

jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Letters only please");

jQuery.validator.addMethod("nowhitespace", function(value, element) {
    return this.optional(element) || /^\S+$/i.test(value);
}, "No white space please");

jQuery.validator.addMethod("ziprange", function(value, element) {
    return this.optional(element) || /^90[2-5]\d\{2\}-\d{4}$/.test(value);
}, "Your ZIP-code must be in the range 902xx-xxxx to 905-xx-xxxx");

jQuery.validator.addMethod("zipcodeUS", function(value, element) {
    return this.optional(element) || /\d{5}-\d{4}$|^\d{5}$/.test(value);
}, "The specified US ZIP Code is invalid");


/**
 * Return true, if the value is a valid vehicle identification number (VIN).
 *
 * Works with all kind of text inputs.
 *
 * @example <input type="text" size="20" name="VehicleID" class="{required:true,vinUS:true}" />
 * @desc Declares a required input element whose value must be a valid vehicle identification number.
 *
 * @name jQuery.validator.methods.vinUS
 * @type Boolean
 * @cat Plugins/Validate/Methods
 */
jQuery.validator.addMethod("vinUS", function(v) {
    if (v.length !== 17) {
        return false;
    }
    var i, n, d, f, cd, cdv;
    var LL = ["A", "B", "C", "D", "E", "F", "G", "H", "J", "K", "L", "M", "N", "P", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
    var VL = [1, 2, 3, 4, 5, 6, 7, 8, 1, 2, 3, 4, 5, 7, 9, 2, 3, 4, 5, 6, 7, 8, 9];
    var FL = [8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2];
    var rs = 0;
    for (i = 0; i < 17; i++) {
        f = FL[i];
        d = v.slice(i, i + 1);
        if (i === 8) {
            cdv = d;
        }
        if (!isNaN(d)) {
            d *= f;
        } else {
            for (n = 0; n < LL.length; n++) {
                if (d.toUpperCase() === LL[n]) {
                    d = VL[n];
                    d *= f;
                    if (isNaN(cdv) && n === 8) {
                        cdv = LL[n];
                    }
                    break;
                }
            }
        }
        rs += d;
    }
    cd = rs % 11;
    if (cd === 10) {
        cd = "X";
    }
    if (cd === cdv) {
        return true;
    }
    return false;
}, "The specified vehicle identification number (VIN) is invalid.");

/**
 * Return true, if the value is a valid date, also making this formal check dd/mm/yyyy.
 *
 * @example jQuery.validator.methods.date("01/01/1900")
 * @result true
 *
 * @example jQuery.validator.methods.date("01/13/1990")
 * @result false
 *
 * @example jQuery.validator.methods.date("01.01.1900")
 * @result false
 *
 * @example <input name="pippo" class="{dateITA:true}" />
 * @desc Declares an optional input element whose value must be a valid date.
 *
 * @name jQuery.validator.methods.dateITA
 * @type Boolean
 * @cat Plugins/Validate/Methods
 */
jQuery.validator.addMethod("dateITA", function(value, element) {
    var check = false;
    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    if (re.test(value)) {
        var adata = value.split('/');
        var gg = parseInt(adata[0], 10);
        var mm = parseInt(adata[1], 10);
        var aaaa = parseInt(adata[2], 10);
        var xdata = new Date(aaaa, mm - 1, gg);
        if ((xdata.getFullYear() === aaaa) && (xdata.getMonth() === mm - 1) && (xdata.getDate() === gg)) {
            check = true;
        } else {
            check = false;
        }
    } else {
        check = false;
    }
    return this.optional(element) || check;
}, "Please enter a correct date");

/**
 * IBAN is the international bank account number.
 * It has a country - specific format, that is checked here too
 */
jQuery.validator.addMethod("iban", function(value, element) {
    // some quick simple tests to prevent needless work
    if (this.optional(element)) {
        return true;
    }
    if (!(/^([a-zA-Z0-9]{4} ){2,8}[a-zA-Z0-9]{1,4}|[a-zA-Z0-9]{12,34}$/.test(value))) {
        return false;
    }

    // check the country code and find the country specific format
    var iban = value.replace(/ /g, '').toUpperCase(); // remove spaces and to upper case
    var countrycode = iban.substring(0, 2);
    var bbancountrypatterns = {
        'AL': "\\d{8}[\\dA-Z]{16}",
        'AD': "\\d{8}[\\dA-Z]{12}",
        'AT': "\\d{16}",
        'AZ': "[\\dA-Z]{4}\\d{20}",
        'BE': "\\d{12}",
        'BH': "[A-Z]{4}[\\dA-Z]{14}",
        'BA': "\\d{16}",
        'BR': "\\d{23}[A-Z][\\dA-Z]",
        'BG': "[A-Z]{4}\\d{6}[\\dA-Z]{8}",
        'CR': "\\d{17}",
        'HR': "\\d{17}",
        'CY': "\\d{8}[\\dA-Z]{16}",
        'CZ': "\\d{20}",
        'DK': "\\d{14}",
        'DO': "[A-Z]{4}\\d{20}",
        'EE': "\\d{16}",
        'FO': "\\d{14}",
        'FI': "\\d{14}",
        'FR': "\\d{10}[\\dA-Z]{11}\\d{2}",
        'GE': "[\\dA-Z]{2}\\d{16}",
        'DE': "\\d{18}",
        'GI': "[A-Z]{4}[\\dA-Z]{15}",
        'GR': "\\d{7}[\\dA-Z]{16}",
        'GL': "\\d{14}",
        'GT': "[\\dA-Z]{4}[\\dA-Z]{20}",
        'HU': "\\d{24}",
        'IS': "\\d{22}",
        'IE': "[\\dA-Z]{4}\\d{14}",
        'IL': "\\d{19}",
        'IT': "[A-Z]\\d{10}[\\dA-Z]{12}",
        'KZ': "\\d{3}[\\dA-Z]{13}",
        'KW': "[A-Z]{4}[\\dA-Z]{22}",
        'LV': "[A-Z]{4}[\\dA-Z]{13}",
        'LB': "\\d{4}[\\dA-Z]{20}",
        'LI': "\\d{5}[\\dA-Z]{12}",
        'LT': "\\d{16}",
        'LU': "\\d{3}[\\dA-Z]{13}",
        'MK': "\\d{3}[\\dA-Z]{10}\\d{2}",
        'MT': "[A-Z]{4}\\d{5}[\\dA-Z]{18}",
        'MR': "\\d{23}",
        'MU': "[A-Z]{4}\\d{19}[A-Z]{3}",
        'MC': "\\d{10}[\\dA-Z]{11}\\d{2}",
        'MD': "[\\dA-Z]{2}\\d{18}",
        'ME': "\\d{18}",
        'NL': "[A-Z]{4}\\d{10}",
        'NO': "\\d{11}",
        'PK': "[\\dA-Z]{4}\\d{16}",
        'PS': "[\\dA-Z]{4}\\d{21}",
        'PL': "\\d{24}",
        'PT': "\\d{21}",
        'RO': "[A-Z]{4}[\\dA-Z]{16}",
        'SM': "[A-Z]\\d{10}[\\dA-Z]{12}",
        'SA': "\\d{2}[\\dA-Z]{18}",
        'RS': "\\d{18}",
        'SK': "\\d{20}",
        'SI': "\\d{15}",
        'ES': "\\d{20}",
        'SE': "\\d{20}",
        'CH': "\\d{5}[\\dA-Z]{12}",
        'TN': "\\d{20}",
        'TR': "\\d{5}[\\dA-Z]{17}",
        'AE': "\\d{3}\\d{16}",
        'GB': "[A-Z]{4}\\d{14}",
        'VG': "[\\dA-Z]{4}\\d{16}"
    };
    var bbanpattern = bbancountrypatterns[countrycode];
    // As new countries will start using IBAN in the
    // future, we only check if the countrycode is known.
    // This prevents false negatives, while almost all
    // false positives introduced by this, will be caught
    // by the checksum validation below anyway.
    // Strict checking should return FALSE for unknown
    // countries.
    if (typeof bbanpattern !== 'undefined') {
        var ibanregexp = new RegExp("^[A-Z]{2}\\d{2}" + bbanpattern + "$", "");
        if (!(ibanregexp.test(iban))) {
            return false; // invalid country specific format
        }
    }

    // now check the checksum, first convert to digits
    var ibancheck = iban.substring(4, iban.length) + iban.substring(0, 4);
    var ibancheckdigits = "";
    var leadingZeroes = true;
    var charAt;
    for (var i = 0; i < ibancheck.length; i++) {
        charAt = ibancheck.charAt(i);
        if (charAt !== "0") {
            leadingZeroes = false;
        }
        if (!leadingZeroes) {
            ibancheckdigits += "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf(charAt);
        }
    }

    // calculate the result of: ibancheckdigits % 97
    var cRest = '';
    var cOperator = '';
    for (var p = 0; p < ibancheckdigits.length; p++) {
        var cChar = ibancheckdigits.charAt(p);
        cOperator = '' + cRest + '' + cChar;
        cRest = cOperator % 97;
    }
    return cRest === 1;
}, "Please specify a valid IBAN");

jQuery.validator.addMethod("dateNL", function(value, element) {
    return this.optional(element) || /^(0?[1-9]|[12]\d|3[01])[\.\/\-](0?[1-9]|1[012])[\.\/\-]([12]\d)?(\d\d)$/.test(value);
}, "Please enter a correct date");

/**
 * Dutch phone numbers have 10 digits (or 11 and start with +31).
 */
jQuery.validator.addMethod("phoneNL", function(value, element) {
    return this.optional(element) || /^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)[1-9]((\s|\s?\-\s?)?[0-9]){8}$/.test(value);
}, "Please specify a valid phone number.");

jQuery.validator.addMethod("mobileNL", function(value, element) {
    return this.optional(element) || /^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)6((\s|\s?\-\s?)?[0-9]){8}$/.test(value);
}, "Please specify a valid mobile number");

jQuery.validator.addMethod("postalcodeNL", function(value, element) {
    return this.optional(element) || /^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/.test(value);
}, "Please specify a valid postal code");

/*
 * Dutch bank account numbers (not 'giro' numbers) have 9 digits
 * and pass the '11 check'.
 * We accept the notation with spaces, as that is common.
 * acceptable: 123456789 or 12 34 56 789
 */
jQuery.validator.addMethod("bankaccountNL", function(value, element) {
    if (this.optional(element)) {
        return true;
    }
    if (!(/^[0-9]{9}|([0-9]{2} ){3}[0-9]{3}$/.test(value))) {
        return false;
    }
    // now '11 check'
    var account = value.replace(/ /g, ''); // remove spaces
    var sum = 0;
    var len = account.length;
    for (var pos = 0; pos < len; pos++) {
        var factor = len - pos;
        var digit = account.substring(pos, pos + 1);
        sum = sum + factor * digit;
    }
    return sum % 11 === 0;
}, "Please specify a valid bank account number");

/**
 * Dutch giro account numbers (not bank numbers) have max 7 digits
 */
jQuery.validator.addMethod("giroaccountNL", function(value, element) {
    return this.optional(element) || /^[0-9]{1,7}$/.test(value);
}, "Please specify a valid giro account number");

jQuery.validator.addMethod("bankorgiroaccountNL", function(value, element) {
    return this.optional(element) ||
            ($.validator.methods["bankaccountNL"].call(this, value, element)) ||
            ($.validator.methods["giroaccountNL"].call(this, value, element));
}, "Please specify a valid bank or giro account number");


jQuery.validator.addMethod("time", function(value, element) {
    return this.optional(element) || /^([01]\d|2[0-3])(:[0-5]\d){1,2}$/.test(value);
}, "Please enter a valid time, between 00:00 and 23:59");
jQuery.validator.addMethod("time12h", function(value, element) {
    return this.optional(element) || /^((0?[1-9]|1[012])(:[0-5]\d){1,2}(\ ?[AP]M))$/i.test(value);
}, "Please enter a valid time in 12-hour am/pm format");

/**
 * matches US phone number format
 *
 * where the area code may not start with 1 and the prefix may not start with 1
 * allows '-' or ' ' as a separator and allows parens around area code
 * some people may want to put a '1' in front of their number
 *
 * 1(212)-999-2345 or
 * 212 999 2344 or
 * 212-999-0983
 *
 * but not
 * 111-123-5434
 * and not
 * 212 123 4567
 */
jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
    phone_number = phone_number.replace(/\s+/g, "");
    return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
}, "Please specify a valid phone number");

jQuery.validator.addMethod('phoneUK', function(phone_number, element) {
    phone_number = phone_number.replace(/\(|\)|\s+|-/g, '');
    return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(?:(?:(?:00\s?|\+)44\s?)|(?:\(?0))(?:\d{2}\)?\s?\d{4}\s?\d{4}|\d{3}\)?\s?\d{3}\s?\d{3,4}|\d{4}\)?\s?(?:\d{5}|\d{3}\s?\d{3})|\d{5}\)?\s?\d{4,5})$/);
}, 'Please specify a valid phone number');

jQuery.validator.addMethod('mobileUK', function(phone_number, element) {
    phone_number = phone_number.replace(/\(|\)|\s+|-/g, '');
    return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(?:(?:(?:00\s?|\+)44\s?|0)7(?:[45789]\d{2}|624)\s?\d{3}\s?\d{3})$/);
}, 'Please specify a valid mobile number');

//Matches UK landline + mobile, accepting only 01-3 for landline or 07 for mobile to exclude many premium numbers
jQuery.validator.addMethod('phonesUK', function(phone_number, element) {
    phone_number = phone_number.replace(/\(|\)|\s+|-/g, '');
    return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(?:(?:(?:00\s?|\+)44\s?|0)(?:1\d{8,9}|[23]\d{9}|7(?:[45789]\d{8}|624\d{6})))$/);
}, 'Please specify a valid uk phone number');
// On the above three UK functions, do the following server side processing:
//  Compare original input with this RegEx pattern:
//   ^\(?(?:(?:00\)?[\s\-]?\(?|\+)(44)\)?[\s\-]?\(?(?:0\)?[\s\-]?\(?)?|0)([1-9]\d{1,4}\)?[\s\d\-]+)$
//  Extract $1 and set $prefix to '+44<space>' if $1 is '44', otherwise set $prefix to '0'
//  Extract $2 and remove hyphens, spaces and parentheses. Phone number is combined $prefix and $2.
// A number of very detailed GB telephone number RegEx patterns can also be found at:
// http://www.aa-asterisk.org.uk/index.php/Regular_Expressions_for_Validating_and_Formatting_GB_Telephone_Numbers

// Matches UK postcode. Does not match to UK Channel Islands that have their own postcodes (non standard UK)
jQuery.validator.addMethod('postcodeUK', function(value, element) {
    return this.optional(element) || /^((([A-PR-UWYZ][0-9])|([A-PR-UWYZ][0-9][0-9])|([A-PR-UWYZ][A-HK-Y][0-9])|([A-PR-UWYZ][A-HK-Y][0-9][0-9])|([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY]))\s?([0-9][ABD-HJLNP-UW-Z]{2})|(GIR)\s?(0AA))$/i.test(value);
}, 'Please specify a valid UK postcode');

// TODO check if value starts with <, otherwise don't try stripping anything
jQuery.validator.addMethod("strippedminlength", function(value, element, param) {
    return jQuery(value).text().length >= param;
}, jQuery.validator.format("Please enter at least {0} characters"));

// same as email, but TLD is optional
jQuery.validator.addMethod("email2", function(value, element, param) {
    return this.optional(element) || /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(value);
}, jQuery.validator.messages.email);

// same as url, but TLD is optional
jQuery.validator.addMethod("url2", function(value, element, param) {
    return this.optional(element) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
}, jQuery.validator.messages.url);

// NOTICE: Modified version of Castle.Components.Validator.CreditCardValidator
// Redistributed under the the Apache License 2.0 at http://www.apache.org/licenses/LICENSE-2.0
// Valid Types: mastercard, visa, amex, dinersclub, enroute, discover, jcb, unknown, all (overrides all other settings)
jQuery.validator.addMethod("creditcardtypes", function(value, element, param) {
    if (/[^0-9\-]+/.test(value)) {
        return false;
    }

    value = value.replace(/\D/g, "");

    var validTypes = 0x0000;

    if (param.mastercard) {
        validTypes |= 0x0001;
    }
    if (param.visa) {
        validTypes |= 0x0002;
    }
    if (param.amex) {
        validTypes |= 0x0004;
    }
    if (param.dinersclub) {
        validTypes |= 0x0008;
    }
    if (param.enroute) {
        validTypes |= 0x0010;
    }
    if (param.discover) {
        validTypes |= 0x0020;
    }
    if (param.jcb) {
        validTypes |= 0x0040;
    }
    if (param.unknown) {
        validTypes |= 0x0080;
    }
    if (param.all) {
        validTypes = 0x0001 | 0x0002 | 0x0004 | 0x0008 | 0x0010 | 0x0020 | 0x0040 | 0x0080;
    }
    if (validTypes & 0x0001 && /^(5[12345])/.test(value)) { //mastercard
        return value.length === 16;
    }
    if (validTypes & 0x0002 && /^(4)/.test(value)) { //visa
        return value.length === 16;
    }
    if (validTypes & 0x0004 && /^(3[47])/.test(value)) { //amex
        return value.length === 15;
    }
    if (validTypes & 0x0008 && /^(3(0[012345]|[68]))/.test(value)) { //dinersclub
        return value.length === 14;
    }
    if (validTypes & 0x0010 && /^(2(014|149))/.test(value)) { //enroute
        return value.length === 15;
    }
    if (validTypes & 0x0020 && /^(6011)/.test(value)) { //discover
        return value.length === 16;
    }
    if (validTypes & 0x0040 && /^(3)/.test(value)) { //jcb
        return value.length === 16;
    }
    if (validTypes & 0x0040 && /^(2131|1800)/.test(value)) { //jcb
        return value.length === 15;
    }
    if (validTypes & 0x0080) { //unknown
        return true;
    }
    return false;
}, "Please enter a valid credit card number.");

jQuery.validator.addMethod("ipv4", function(value, element, param) {
    return this.optional(element) || /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(value);
}, "Please enter a valid IP v4 address.");

jQuery.validator.addMethod("ipv6", function(value, element, param) {
    return this.optional(element) || /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
}, "Please enter a valid IP v6 address.");

/**
 * Return true if the field value matches the given format RegExp
 *
 * @example jQuery.validator.methods.pattern("AR1004",element,/^AR\d{4}$/)
 * @result true
 *
 * @example jQuery.validator.methods.pattern("BR1004",element,/^AR\d{4}$/)
 * @result false
 *
 * @name jQuery.validator.methods.pattern
 * @type Boolean
 * @cat Plugins/Validate/Methods
 */
jQuery.validator.addMethod("pattern", function(value, element, param) {

    // ignore if empty
    if (param == '') {
        return true;
    }
    if (this.optional(element)) {
        return true;
    }
    if (typeof param === 'string') {
        param = new RegExp('^(?:' + param + ')$');
    }
    return param.test(value);
}, "Invalid format.");

jQuery.validator.addMethod("integer", function(value, element, param) {
    return this.optional(element) || /^-?\d+$/.test(value);
}, "{1}");


/*
 * Lets you say "at least X inputs that match selector Y must be filled."
 *
 * The end result is that neither of these inputs:
 *
 *  <input class="productinfo" name="partnumber">
 *  <input class="productinfo" name="description">
 *
 *  ...will validate unless at least one of them is filled.
 *
 * partnumber:  {require_from_group: [1,".productinfo"]},
 * description: {require_from_group: [1,".productinfo"]}
 *
 */
jQuery.validator.addMethod("require_from_group", function(value, element, options) {
    var validator = this;
    var selector = options[1];
    var validOrNot = $(selector, element.form).filter(function() {
        return validator.elementValue(this);
    }).length >= options[0];

    if (!$(element).data('being_validated')) {
        var fields = $(selector, element.form);
        fields.data('being_validated', true);
        fields.valid();
        $(element.form).valid();
        fields.data('being_validated', false);
    }
    return validOrNot;
}, jQuery.format("Please fill at least {0} of these fields."));

/*
 * Lets you say "either at least X inputs that match selector Y must be filled,
 * OR they must all be skipped (left blank)."
 *
 * The end result, is that none of these inputs:
 *
 *  <input class="productinfo" name="partnumber">
 *  <input class="productinfo" name="description">
 *  <input class="productinfo" name="color">
 *
 *  ...will validate unless either at least two of them are filled,
 *  OR none of them are.
 *
 * partnumber:  {skip_or_fill_minimum: [2,".productinfo"]},
 *  description: {skip_or_fill_minimum: [2,".productinfo"]},
 * color:       {skip_or_fill_minimum: [2,".productinfo"]}
 *
 */
jQuery.validator.addMethod("skip_or_fill_minimum", function(value, element, options) {
    var validator = this,
            numberRequired = options[0],
            selector = options[1];
    var numberFilled = $(selector, element.form).filter(function() {
        return validator.elementValue(this);
    }).length;
    var valid = numberFilled >= numberRequired || numberFilled === 0;

    if (!$(element).data('being_validated')) {
        var fields = $(selector, element.form);
        fields.data('being_validated', true);
        fields.valid();
        fields.data('being_validated', false);
    }
    return valid;
}, jQuery.format("Please either skip these fields or fill at least {0} of them."));

// Accept a value from a file input based on a required mimetype
jQuery.validator.addMethod("accept", function(value, element, param) {
    // Split mime on commas in case we have multiple types we can accept
    var typeParam = typeof param === "string" ? param.replace(/\s/g, '').replace(/,/g, '|') : "image/*",
            optionalValue = this.optional(element),
            i, file;

    // Element is optional
    if (optionalValue) {
        return optionalValue;
    }

    if ($(element).attr("type") === "file") {
        // If we are using a wildcard, make it regex friendly
        typeParam = typeParam.replace(/\*/g, ".*");

        // Check if the element has a FileList before checking each file
        if (element.files && element.files.length) {
            for (i = 0; i < element.files.length; i++) {
                file = element.files[i];

                // Grab the mimetype from the loaded file, verify it matches
                if (!file.type.match(new RegExp(".?(" + typeParam + ")$", "i"))) {
                    return false;
                }
            }
        }
    }

    // Either return true because we've validated each file, or because the
    // browser does not support element.files and the FileList feature
    return true;
}, jQuery.format("Please enter a value with a valid mimetype."));

// Older "accept" file extension method. Old docs: http://docs.jquery.com/Plugins/Validation/Methods/accept
jQuery.validator.addMethod("extension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, jQuery.format("Please enter a value with a valid extension."));

// not equal to other field
jQuery.validator.addMethod("notEqualTo", function(value, element, param) {
    return this.optional(element) || value != param;
}, "Please specify a different (non-default) value");

jQuery.validator.addMethod('empty', function(value, element) {
    return (value === '');
}, "This field must remain empty!");

jQuery.validator.addMethod('notempty', function(value, element) {
    if (!value) {
        return false;
    }
    return (value !== '');
}, "This field must have an answer!");

/* allow only one question to be answered in a group */
jQuery.validator.addMethod('exclusive', function(value, el, args) {
    var count = 0;
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        var el = $('[name="' + v + '"]');
        var type = el.attr('type');
        if (type == 'radio') {
            var val = $('[name="' + v + '"]:checked').val();
        }
        else {
            var val = el.val();
        }
        if (val) {
            count++;
        }
        if (count > 1) {
            return false;
        }
    }
    return true;
}
, jQuery.validator.format('{1}'));

/* all questions must be answered in a group */
jQuery.validator.addMethod('inclusive', function(value, el, args) {
    for (var i = 0, limit = args.length; i < limit; ++i) {
        //var name = $(args[i]).attr("name");
        //var val = $('[name="' + name + '"]').val();
        var v = args[i].replace("'", "").replace("'", "");
        var el = $('[name="' + v + '"]');
        var type = el.attr('type');
        if (type == 'radio') {
            var val = $('[name="' + v + '"]:checked').val();
        }
        else {
            var val = el.val();
        }
        if (!val || val == "") {
            return false;
        }
    }
    return true;
}
, jQuery.validator.format('{1}'));

/* minimum number of questions answered in a group */
jQuery.validator.addMethod("minimumrequired", function(value, element, args) {
    var ar = args.split("-");
    var minrequired = args[0];
    var temp = ar[1];
    var fields = temp.replaceAll("[]", "BRACKCOMBI");
    fields = fields.replaceAll("[", "").replaceAll("]", "");
    fields = fields.replaceAll("BRACKCOMBI", "[]");
    var count = 0;
    fields = fields.split(",");
    for (var i = 0; i < fields.length; i++) {
        var v = fields[i].replace("'", "").replace("'", "");
        var el = $('[name="' + v + '"]');
        var type = el.attr('type');
        if (type == 'radio') {
            var val = $('[name="' + v + '"]:checked').val();
        }
        else if (type == 'checkbox') {
            var myarray = [];
            $('[name="' + v + '"]:checked').each(function(){
                myarray.push($(this).val());
            });
            var val = myarray.join("-");
        }
        else {
            var val = el.val();
        }
        if (val && val != "") {
            count = count + 1;
        }
    }
    //alert(count);    
    if (minrequired > count) {
        return false;
    }
    return true;
}, jQuery.format("Please answer at least {0} questions."));

/* maximum number of questions answered in a group */
jQuery.validator.addMethod("maximumrequired", function(value, element, args) {
    var ar = args.split("-");
    var maxrequired = args[0];
    var temp = ar[1];
    var fields = temp.replaceAll("[]", "BRACKCOMBI");
    fields = fields.replaceAll("[", "").replaceAll("]", "");
    fields = fields.replaceAll("BRACKCOMBI", "[]");
    var count = 0;
    fields = fields.split(",");
    for (var i = 0; i < fields.length; i++) {
        var v = fields[i].replace("'", "").replace("'", "");
        var el = $('[name="' + v + '"]');
        var type = el.attr('type');
        if (type == 'radio') {
            var val = $('[name="' + v + '"]:checked').val();
        }
        else if (type == 'checkbox') {
            var myarray = [];
            $('[name="' + v + '"]:checked').each(function(){
                myarray.push($(this).val());
            });
            var val = myarray.join("-");
        }
        else {
            var val = el.val();
        }
        if (val && val != "") {
            count = count + 1;
        }
    }

    if (maxrequired < count) {
        return false;
    }
    return true;
}, jQuery.format("Please answer at most {0} questions."));

/* exact number of questions answered in a group */
jQuery.validator.addMethod("exactrequired", function(value, element, args) {
    var ar = args.split("-");
    var exactrequired = args[0];
    var temp = ar[1];
    var fields = temp.replaceAll("[]", "BRACKCOMBI");
    fields = fields.replaceAll("[", "").replaceAll("]", "");
    fields = fields.replaceAll("BRACKCOMBI", "[]");
    var count = 0;
    fields = fields.split(",");
    for (var i = 0; i < fields.length; i++) {
        var v = fields[i].replace("'", "").replace("'", "");
        var el = $('[name="' + v + '"]');
        var type = el.attr('type');
        if (type == 'radio') {
            var val = $('[name="' + v + '"]:checked').val();
        }
        else if (type == 'checkbox') {
            var myarray = [];
            $('[name="' + v + '"]:checked').each(function(){
                myarray.push($(this).val());
            });
            var val = myarray.join("-");
        }
        else {
            var val = el.val();
        }
        if (val && val != "") {
            count = count + 1;
        }
    }
    //alert(count);    
    if (exactrequired != count) {
        return false;
    }
    return true;
}, jQuery.format("Please answer exactly {0} questions."));

/* unique answers to questions in a group */
jQuery.validator.addMethod("uniquerequired", function(value, element, args) {
    var values = new Array();
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]')) {
            var el = $('[name="' + v + '"]');
            var type = el.attr('type');
            if (type == 'radio') {
                var val = $('[name="' + v + '"]:checked').val();
            }
            else if (type == 'checkbox') {
                var myarray = [];
                $('[name="' + v + '"]:checked').each(function(){
                    myarray.push($(this).val());
                });
                var val = myarray.sort(sortNumber).join("-");
            }            
            else {
                var val = el.val();
            }
            if (val && val !== "") {

                // already have value, then false
                //alert(val);
                var index = jQuery.inArrayIn(val, values);
                if (index > -1) {
                    return false;
                }
                //alert(values.join(","));
                values.push(val);
            }
        }
    }
    return true;
}, jQuery.format("Please give an unique answer to all questions."));

/* same answers to questions in a group */
jQuery.validator.addMethod("samerequired", function(value, element, args) {

    var values = new Array();

    for (var i = 0, limit = args.length; i < limit; ++i) {

        var v = args[i].replace("'", "").replace("'", "");
        //alert($('[name="' + v + '"]'));
        if ($('[name="' + v + '"]')) {
            //alert('ttttt' + v);
            var el = $('[name="' + v + '"]');
            var type = el.attr('type');
            if (type == 'radio') {
                var val = $('[name="' + v + '"]:checked').val();
            }
            else if (type == 'checkbox') {
                var myarray = [];
                $('[name="' + v + '"]:checked').each(function(){
                    myarray.push($(this).val());
                });
                var val = myarray.sort(sortNumber).join("-");
            } 
            else {
                var val = el.val();
            }

            if (val && val !== "") {

                // check if set of enum val

                // already have this value
                //alert(v + ": " + val);
                var index = jQuery.inArrayIn(val, values);
                if (index > -1) {

                }
                else {
                    values.push(val);
                }
            }

            // not all the same value
            //alert(values.join(","));
            if (values.length > 1) {
                return false;
            }
        }
    }
    return true;
}, jQuery.format("Please give the same answer to all questions."));

/* ranked */
jQuery.validator.addMethod('minimumranked', function(value, element, param) {
    var name = $(element).attr("name");
    var count = $("#" + name + "_counter").val();
    return count >= param;
}, 'Please rank {0} or more options');

jQuery.validator.addMethod('exactranked', function(value, element, param) {
    var name = $(element).attr("name");
    var count = $("#" + name + "_counter").val();
    return count == param;
}, 'Please rank {0} or more options');

jQuery.validator.addMethod('maximumranked', function(value, element, param) {
    var name = $(element).attr("name");
    var count = $("#" + name + "_counter").val();
    return count <= param;
}, 'Please rank {0} or less options');

/* set of enumerated */
jQuery.validator.addMethod('minimumselected', function(value, element, param) {
    var name = $(element).attr("name");
    return $('[name="' + name + '"]:checked').length >= param;
}, 'Please select {0} or more options');

jQuery.validator.addMethod('exactselected', function(value, element, param) {
    var name = $(element).attr("name");
    return $('[name="' + name + '"]:checked').length == param;
}, 'Please select {0} or more options');

jQuery.validator.addMethod('maximumselected', function(value, element, param) {
    var name = $(element).attr("name");
    return $('[name="' + name + '"]:checked').length <= param;
}, 'Please select {0} or less options');

jQuery.validator.addMethod('invalidsubselected', function(value, element, param) {
    var name = $(element).attr("name");
    var invalids = param.split("#");
    var indices = new Array();
    $('[name="' + name + '"]:checked').each(function() {
        indices.push(parseInt($(this).val()));
    });
    for (var c = 0; c < invalids.length; c++) {
        var invalid = invalids[c].split(",");
        //alert('hhhh' + indices.join(","));
        var selected = new Array();
        for (var cnt = 0; cnt < invalid.length; cnt++) {
            var inv = invalid[cnt];

            // contains -, so this is a range!
            if (inv.indexOf("-") > -1) {
                var t = inv.split("-");
                var all = true;
                for (var cnt1 = t[0]; cnt1 <= t[1]; cnt1++) {
                    //alert(indices.indexOf(parseInt(cnt1)));
                    if (indices.indexOf(parseInt(cnt1)) === -1) {
                        //alert(indices.join(","));
                        //alert('oh' + cnt1);
                        all = false;
                        break;
                    }
                }
                if (all == true) {
                    //alert('all found!');
                    selected[cnt] = 0;
                }
                else {
                    selected[cnt] = -1;
                }
            }
            else {
                selected[cnt] = indices.indexOf(parseInt(inv)); // returns -1 if not found
            }
        }

        // no -1, then all found, so invalid
        if (jQuery.inArray(-1, selected) === -1) {
            return false;
        }
    }
    return true;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('invalidselected', function(value, element, param) {
    var name = $(element).attr("name");
    var invalids = param.split("#");
    var indices = new Array();
    $('[name="' + name + '"]:checked').each(function() {
        indices.push(parseInt($(this).val()));
    });


    for (var c = 0; c < invalids.length; c++) {
        var invalid = invalids[c].split(",");
        //alert('hhhh' + indices.join(","));
        var selected = new Array();
        var invalidselected = new Array();
        for (var cnt = 0; cnt < invalid.length; cnt++) {
            var inv = invalid[cnt];

            // contains -, so this is a range!
            if (inv.indexOf("-") > -1) {
                var t = inv.split("-");
                var all = true;
                for (var cnt1 = t[0]; cnt1 <= t[1]; cnt1++) {
                    //alert(indices.indexOf(parseInt(cnt1)));
                    if (indices.indexOf(parseInt(cnt1)) === -1) {
                        //alert(indices.join(","));
                        //alert('oh' + cnt1);
                        all = false;
                        break;
                    }
                    invalidselected.push(cnt1);
                }
                if (all == true) {
                    //alert('all found!');
                    selected[cnt] = 0;
                }
                else {
                    selected[cnt] = -1;
                }
            }
            else {
                invalidselected.push(inv);
                selected[cnt] = indices.indexOf(parseInt(inv)); // returns -1 if not found
            }
        }

        // no -1, then all found
        if (jQuery.inArray(-1, selected) === -1) {

            // if size of selected indices is the same as the total number selected, then false;
            // otherwise we selected more than the invalid set and we thus allow it
            if (indices.length === invalidselected.length) {// TODO!!! CHECK IF WE HAVE ANOTHER CHECKBOX THAT IS NOT PART OF THE INVALIDLY SELECTED ONES!
                return false;
            }
        }
    }
    return true;
}, 'Please select a non-invalid combination');

/* multi select dropdown */
jQuery.validator.addMethod('minimumselecteddropdown', function(value, element, param) {
    var name = $(element).attr("name");
    return $('[name="' + name + '"] option:selected').length >= param;
}, 'Please select {0} or more options');

jQuery.validator.addMethod('exactselecteddropdown', function(value, element, param) {
    var name = $(element).attr("name");
    return $('[name="' + name + '"] option:selected').length == param;
}, 'Please select {0} or more options');

jQuery.validator.addMethod('maximumselecteddropdown', function(value, element, param) {
    var name = $(element).attr("name");
    return $('[name="' + name + '"] option:selected').length <= param;
}, 'Please select {0} or less options');

jQuery.validator.addMethod('invalidsubselecteddropdown', function(value, element, param) {
    var name = $(element).attr("name");
    var invalids = param.split("#");
    var indices = new Array();
    $('[name="' + name + '"] option:selected').each(function() {
        indices.push(parseInt($(this).val()));
    });
    for (var c = 0; c < invalids.length; c++) {
        var invalid = invalids[c].split(",");

        //alert('hhhh' + indices.join(","));
        var selected = new Array();
        for (var cnt = 0; cnt < invalid.length; cnt++) {
            var inv = invalid[cnt];

            // contains -, so this is a range!
            if (inv.indexOf("-") > -1) {
                var t = inv.split("-");
                var all = true;
                for (var cnt1 = t[0]; cnt1 <= t[1]; cnt1++) {
                    //alert(indices.indexOf(parseInt(cnt1)));
                    if (indices.indexOf(parseInt(cnt1)) === -1) {
                        //alert(indices.join(","));
                        //alert('oh' + cnt1);
                        all = false;
                        break;
                    }
                }
                if (all == true) {
                    //alert('all found!');
                    selected[cnt] = 0;
                }
                else {
                    selected[cnt] = -1;
                }
            }
            else {
                selected[cnt] = indices.indexOf(parseInt(inv)); // returns -1 if not found
            }
        }

        // no -1, then all found, so invalid
        if (jQuery.inArray(-1, selected) === -1) {
            return false;
        }
    }
    return true;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('invalidselecteddropdown', function(value, element, param) {
    var name = $(element).attr("name");
    var invalids = param.split("#");
    var indices = new Array();
    $('[name="' + name + '"] option:selected').each(function() {
        indices.push(parseInt($(this).val()));
    });
    for (var c = 0; c < invalids.length; c++) {
        var invalid = invalids[c].split(",");
        var selected = new Array();
        var invalidselected = new Array();
        for (var cnt = 0; cnt < invalid.length; cnt++) {
            var inv = invalid[cnt];

            // contains -, so this is a range!
            if (inv.indexOf("-") > -1) {
                var t = inv.split("-");
                var all = true;
                for (var cnt1 = t[0]; cnt1 <= t[1]; cnt1++) {
                    //alert(indices.indexOf(parseInt(cnt1)));
                    if (indices.indexOf(parseInt(cnt1)) === -1) {
                        //alert(indices.join(","));
                        //alert('oh' + cnt1);
                        all = false;
                        break;
                    }
                    invalidselected.push(cnt1);
                }
                if (all == true) {
                    //alert('all found!');
                    selected[cnt] = 0;
                }
                else {
                    selected[cnt] = -1;
                }
            }
            else {
                invalidselected.push(inv);
                selected[cnt] = indices.indexOf(parseInt(inv)); // returns -1 if not found
            }
        }

        // no -1, then all found
        if (jQuery.inArray(-1, selected) === -1) {

            // if size of selected indices is the same as the total number selected, then false;
            // otherwise we selected more than the invalid set and we thus allow it
            if (indices.length === invalidselected.length) {
                return false;
            }
        }
    }
    return true;
}, 'Please select a non-invalid combination');


/* INLINE FIELD ERROR CHECKS */

/* require one inline question to be answered in a radiobutton/checkbox */
jQuery.validator.addMethod('inlineexclusive', function(value, el, args) {
    var count = 0;
    var result = true;
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var id = $(args[i]).attr('id');
        var allInputs = $(":input[linkedto='" + id + "']"); // "label[for='" + id + "']" 
        allInputs.each(function(index) {
            var val = $(this).val();
            var name = $(this).prop('name');
            var dkrfna = false;

            // check for individual dk/rf/na
            if ($("input[name=\'" + name + "_dkrfna']:checked").val()) {
                dkrfna = true;
            }

            if (val !== "" || dkrfna == true) {

                /* more than one answered */
                if (count > 1) {
                    result = false;
                    return false;
                }
                count = count + 1;
            }
        });

        if (result == false) {
            break;
        }
    }

    return result;
}, 'Please select a non-invalid combination');


//* require all inline question(s) to be answered in a radiobutton/checkbox if it has been checked */
jQuery.validator.addMethod('inlineinclusive', function(value, el, args) {
    var count = 0;
    var result = true;
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var id = $(args[i]).attr('id');
        var checked = $(args[i]).prop("checked");
        if (checked) {
            var allInputs = $(":input[linkedto='" + id + "']"); // "label[for='" + id + "']"             
            if (allInputs.length > 0) {
                allInputs.each(function(index) {
                    var val = $(this).val();
                    var name = $(this).prop('name');
                    if (!val || val === "") {
                        result = false;

                        // check for individual dk/rf/na
                        if ($("input[name=\'" + name + "_dkrfna']:checked").val()) {
                            result = true;
                        }
                        else {
                            return false;
                        }
                    }
                });
            }
        }
        if (result == false) {
            break;
        }
    }

    return result;
}, 'Please select a non-invalid combination');


/* require specific number of inline question(s) to be answered in a radiobutton/checkbox */
jQuery.validator.addMethod('inlineminimumrequired', function(value, el, args) {
    var inlinefields = false;
    var count = 0;
    var ar = args.split("-");
    var minrequired = args[0];
    var fields = ar[1].replace("[", "").replace("]", "");
    var count = 0;
    fields = fields.split(",");
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i].replace("'", "").replace("'", "");
        var allInputs = $(":input[linkedto='" + id + "']"); // "label[for='" + id + "']" 
        allInputs.each(function(index) {
            inlinefields = true;
            var val = $(this).val();
            var name = $(this).prop('name');
            var dkrfna = false;

            // check for individual dk/rf/na
            if ($("input[name=\'" + name + "_dkrfna']:checked").val()) {
                dkrfna = true;
            }

            if (val != "" || dkrfna == true) {
                count = count + 1;
            }
        });
    }

    /* no inline fields */
    if (inlinefields == false) {
        return true;
    }

    if (count < minrequired) {
        return false;
    }
    return true;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('inlinemaximumrequired', function(value, el, args) {
    var inlinefields = false;
    var count = 0;
    var ar = args.split("-");
    var maxrequired = args[0];
    var fields = ar[1].replace("[", "").replace("]", "");
    var count = 0;
    fields = fields.split(",");
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i].replace("'", "").replace("'", "");
        var allInputs = $(":input[linkedto='" + id + "']"); // "label[for='" + id + "']" 
        allInputs.each(function(index) {
            inlinefields = true;
            var val = $(this).val();
            var name = $(this).prop('name');
            var dkrfna = false;

            // check for individual dk/rf/na
            if ($("input[name=\'" + name + "_dkrfna']:checked").val()) {
                dkrfna = true;
            }

            if (val != "" || dkrfna == true) {
                count = count + 1;
            }
        });
    }

    /* no inline fields */
    if (inlinefields == false) {
        return true;
    }

    if (count > maxrequired) {
        return false;
    }
    return true;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('inlineexactrequired', function(value, el, args) {
    var inlinefields = false;
    var count = 0;
    var ar = args.split("-");
    var exactrequired = args[0];
    var fields = ar[1].replace("[", "").replace("]", "");
    var count = 0;
    fields = fields.split(",");
    for (var i = 0; i < fields.length; i++) {
        var id = fields[i].replace("'", "").replace("'", "");
        var allInputs = $(":input[linkedto='" + id + "']"); // "label[for='" + id + "']" 
        allInputs.each(function(index) {
            inlinefields = true;
            var val = $(this).val();
            var name = $(this).prop('name');
            var dkrfna = false;

            // check for individual dk/rf/na
            if ($("input[name=\'" + name + "_dkrfna']:checked").val()) {
                dkrfna = true;
            }

            if (val != "" || dkrfna == true) {
                count = count + 1;
            }
        });
    }

    /* no inline fields */
    if (inlinefields == false) {
        return true;
    }

    if (exactrequired != count) {
        return false;
    }
    return true;
}, 'Please select a non-invalid combination');


jQuery.validator.addMethod('inlineanswered', function(value, el, args) {

    var inlinefields = false;
    var result = true;
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var id = $(args[i]).prop('id');
        var allInputs = $(":input[linkedto='" + id + "']"); // "label[for='" + id + "']" 
        //var allInputs = label.find(":input");   
        var checked = $(args[i]).prop("checked");
        if (checked === false) {
            allInputs.each(function(index) {
                var val = $(this).val();
                var name = $(this).prop('name');
                var answered = false;
                if (val !== "" && endsWith(name, "_dkrfna") === false) { // value and not dk/rf/na switch
                    result = false;
                    return false;
                }
                else if (endsWith(name, "_dkrfna") === true && $(this).is(':checked')) { // check for dk/rf/na
                    result = false;
                    return false;
                }
            });
        }
        if (result == false) {
            break;
        }
    }

    return result;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('enumeratedentered', function(value, el, args) {
    var value = $("#" + $(el).attr('name') + "_textbox").val();
    if (!value || value == "" || value === undefined) {
        return true;
    }


    var ar = args.split("-");
    for (var i = 0; i < ar.length; i++) {
        if (ar[i] == value) {
            return true;
        }
    }
    return false;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('setofenumeratedentered', function(value, el, args) {
    var name = ($(el).attr('name'));
    var targetname = name.replace("_name[]", "");
    var ar = args.split("-");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }

    var values = v.split("-");
    for (var i = 0; i < values.length; i++) {
        var found = false;
        var value = values[i];
        for (var j = 0; j < ar.length; j++) {
            if (ar[j] == value) {
                found = true;
            }
        }
        if (found == false) {
            return false;
        }
    }
    return true;
}, 'Please select a non-invalid combination');

jQuery.validator.addMethod('rangecustom', function(value, el, args) {
    if (!value || value == "" || value === undefined) {
        return false;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    var ar = args.split(";");
    var minmax = ar[0].split(",");
    var others = ar[1].split(",");

    // in other values
    var index = jQuery.inArrayIn(value, others);
    if (index > -1) {
        return true;
    }

    // below minimum
    var min = parseFloat(minmax[0]);
    if (value < min) {
        return false;
    }
    // above maximum
    var max = parseFloat(minmax[1]);
    if (value > max) {
        return false;
    }

    // value in range
    return true;
}, 'Please select a non-invalid combination');


/* numeric comparisons */

/* answer equal to other one(s) */
jQuery.validator.addMethod("setofenum_numeric_equalto", function(value, el, args) {

    // ignore if empty
    if (args == '') {
        return true;
    }

    var name = ($(el).attr('name'));
    var targetname = name.replace("_name", "").replace("[]", "");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }
    v = '' + v;
    if (v.indexOf(",") > -1) { // multi-dropdown
        var values = v.split(",");
    }
    else { // checkboxes        
        var values = v.split("-");
    }
    var requiredvalues = [];
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            requiredvalues.push(v);
        }
        else {

            if ($('[name="' + v + '"]').length > 0 || $('[name="' + v + '[]"]').length > 0) {
                var el = $('[name="' + v + '"]');
                if (el.length == 0) {
                    el = $('[name="' + v + '[]"]');
                    var t = '' + el.val();
                    if (t.indexOf(",") > -1) { // multi-dropdown
                        var vals = t.split(",");
                    }
                    else { // checkboxes
                        var vals = t.split("-");
                    }
                    //var vals = el.val().split("-");
                    //alert(el.val());
                    val = vals.sort(sortNumber).join("-");
                }
                else {
                    var type = el.attr('type');
                    if (type == 'radio') {
                        var val = $('[name="' + v + '"]:checked').val();
                    }
                    else {
                        var val = el.val();
                    }
                }

                var check = val.split("-");
                for (var m = 0, limit2 = check.length; m < limit2; ++m) {
                    var t = check[m];
                    requiredvalues.push(t);
                }
            }
            else {
                continue;
            }
        }
    }

    for (var m = 0, limit2 = requiredvalues.length; m < limit2; ++m) {
        if (jQuery.inArray(requiredvalues[m], values) === -1) {
            return false;
        }
    }

    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer not equal to other one(s) */
jQuery.validator.addMethod("setofenum_numeric_notequalto", function(value, el, args) {

    // ignore if empty
    if (args == '') {
        return true;
    }

    var name = ($(el).attr('name'));
    var targetname = name.replace("_name", "").replace("[]", "");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }
    v = '' + v;
    if (v.indexOf(",") > -1) { // multi-dropdown
        var values = v.split(",");
    }
    else { // checkboxes        
        var values = v.split("-");
    }
    var requiredvalues = [];
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            requiredvalues.push(v);
        }
        else {

            if ($('[name="' + v + '"]').length > 0 || $('[name="' + v + '[]"]').length > 0) {
                var el = $('[name="' + v + '"]');
                if (el.length == 0) {
                    el = $('[name="' + v + '[]"]');
                    var t = '' + el.val();
                    if (t.indexOf(",") > -1) { // multi-dropdown
                        var vals = t.split(",");
                    }
                    else { // checkboxes
                        var vals = t.split("-");
                    }
                    //var vals = el.val().split("-");
                    //alert(el.val());
                    val = vals.sort(sortNumber).join("-");
                }
                else {
                    var type = el.attr('type');
                    if (type == 'radio') {
                        var val = $('[name="' + v + '"]:checked').val();
                    }
                    else {
                        var val = el.val();
                    }
                }

                var check = val.split("-");
                for (var m = 0, limit2 = check.length; m < limit2; ++m) {
                    var t = check[m];
                    requiredvalues.push(t);
                }
            }
            else {
                continue;
            }
        }
    }

    for (var m = 0, limit2 = requiredvalues.length; m < limit2; ++m) {
        if (jQuery.inArray(requiredvalues[m], values) !== -1) {
            return false;
        }
    }

    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* answer greater or equal to other one(s) */
jQuery.validator.addMethod("setofenum_numeric_greaterequalto", function(value, el, args) {

    // ignore if empty
    if (args == '') {
        return true;
    }

    var name = ($(el).attr('name'));
    var targetname = name.replace("_name", "").replace("[]", "");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }
    v = '' + v;
    if (v.indexOf(",") > -1) { // multi-dropdown
        var values = v.split(",");
    }
    else { // checkboxes        
        var values = v.split("-");
    }
    var requiredvalues = [];
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            requiredvalues.push(v);
        }
        else {

            if ($('[name="' + v + '"]').length > 0 || $('[name="' + v + '[]"]').length > 0) {
                var el = $('[name="' + v + '"]');
                if (el.length == 0) {
                    el = $('[name="' + v + '[]"]');
                    var t = '' + el.val();
                    if (t.indexOf(",") > -1) { // multi-dropdown
                        var vals = t.split(",");
                    }
                    else { // checkboxes
                        var vals = t.split("-");
                    }
                    //var vals = el.val().split("-");
                    //alert(el.val());
                    val = vals.sort(sortNumber).join("-");
                }
                else {
                    var type = el.attr('type');
                    if (type == 'radio') {
                        var val = $('[name="' + v + '"]:checked').val();
                    }
                    else {
                        var val = el.val();
                    }
                }

                var check = val.split("-");
                for (var m = 0, limit2 = check.length; m < limit2; ++m) {
                    var t = check[m];
                    requiredvalues.push(t);
                }
            }
            else {
                continue;
            }
        }
    }

    for (var m = 0, limit2 = requiredvalues.length; m < limit2; ++m) {
        for (j = 0; j < values.length; j++) {
            if (parseFloat(values[j]) < parseFloat(requiredvalues[m])) {
                return false;
            }
        }
    }

    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer greater than other one(s) */
jQuery.validator.addMethod("setofenum_numeric_greater", function(value, el, args) {
    // ignore if empty
    if (args == '') {
        return true;
    }

    var name = ($(el).attr('name'));
    var targetname = name.replace("_name", "").replace("[]", "");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }
    v = '' + v;
    if (v.indexOf(",") > -1) { // multi-dropdown
        var values = v.split(",");
    }
    else { // checkboxes        
        var values = v.split("-");
    }
    var requiredvalues = [];
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            requiredvalues.push(v);
        }
        else {

            if ($('[name="' + v + '"]').length > 0 || $('[name="' + v + '[]"]').length > 0) {
                var el = $('[name="' + v + '"]');
                if (el.length == 0) {
                    el = $('[name="' + v + '[]"]');
                    var t = '' + el.val();
                    if (t.indexOf(",") > -1) { // multi-dropdown
                        var vals = t.split(",");
                    }
                    else { // checkboxes
                        var vals = t.split("-");
                    }
                    //var vals = el.val().split("-");
                    //alert(el.val());
                    val = vals.sort(sortNumber).join("-");
                }
                else {
                    var type = el.attr('type');
                    if (type == 'radio') {
                        var val = $('[name="' + v + '"]:checked').val();
                    }
                    else {
                        var val = el.val();
                    }
                }

                var check = val.split("-");
                for (var m = 0, limit2 = check.length; m < limit2; ++m) {
                    var t = check[m];
                    requiredvalues.push(t);
                }
            }
            else {
                continue;
            }
        }
    }

    for (var m = 0, limit2 = requiredvalues.length; m < limit2; ++m) {
        for (j = 0; j < values.length; j++) {
            if (parseFloat(values[j]) <= parseFloat(requiredvalues[m])) {
                return false;
            }
        }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller or equal to other one(s) */
jQuery.validator.addMethod("setofenum_numeric_smallerequalto", function(value, el, args) {
    // ignore if empty
    if (args == '') {
        return true;
    }

    var name = ($(el).attr('name'));
    var targetname = name.replace("_name", "").replace("[]", "");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }
    v = '' + v;
    if (v.indexOf(",") > -1) { // multi-dropdown
        var values = v.split(",");
    }
    else { // checkboxes        
        var values = v.split("-");
    }
    var requiredvalues = [];
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            requiredvalues.push(v);
        }
        else {

            if ($('[name="' + v + '"]').length > 0 || $('[name="' + v + '[]"]').length > 0) {
                var el = $('[name="' + v + '"]');
                if (el.length == 0) {
                    el = $('[name="' + v + '[]"]');
                    var t = '' + el.val();
                    if (t.indexOf(",") > -1) { // multi-dropdown
                        var vals = t.split(",");
                    }
                    else { // checkboxes
                        var vals = t.split("-");
                    }
                    //var vals = el.val().split("-");
                    //alert(el.val());
                    val = vals.sort(sortNumber).join("-");
                }
                else {
                    var type = el.attr('type');
                    if (type == 'radio') {
                        var val = $('[name="' + v + '"]:checked').val();
                    }
                    else {
                        var val = el.val();
                    }
                }

                var check = val.split("-");
                for (var m = 0, limit2 = check.length; m < limit2; ++m) {
                    var t = check[m];
                    requiredvalues.push(t);
                }
            }
            else {
                continue;
            }
        }
    }

    for (var m = 0, limit2 = requiredvalues.length; m < limit2; ++m) {
        for (j = 0; j < values.length; j++) {
            if (parseFloat(values[j]) > parseFloat(requiredvalues[m])) {
                return false;
            }
        }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller than other one(s) */
jQuery.validator.addMethod("setofenum_numeric_smaller", function(value, el, args) {
    // ignore if empty
    if (args == '') {
        return true;
    }

    var name = ($(el).attr('name'));
    var targetname = name.replace("_name", "").replace("[]", "");
    var v = $('[name="' + targetname + '[]"]').val();
//    var v = $("#" + targetname + "_textbox").val();
    if (!v || v == "") {
        return true;
    }
    v = '' + v;
    if (v.indexOf(",") > -1) { // multi-dropdown
        var values = v.split(",");
    }
    else { // checkboxes        
        var values = v.split("-");
    }
    var requiredvalues = [];
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            requiredvalues.push(v);
        }
        else {

            if ($('[name="' + v + '"]').length > 0 || $('[name="' + v + '[]"]').length > 0) {
                var el = $('[name="' + v + '"]');
                if (el.length == 0) {
                    el = $('[name="' + v + '[]"]');
                    var t = '' + el.val();
                    if (t.indexOf(",") > -1) { // multi-dropdown
                        var vals = t.split(",");
                    }
                    else { // checkboxes
                        var vals = t.split("-");
                    }
                    //var vals = el.val().split("-");
                    //alert(el.val());
                    val = vals.sort(sortNumber).join("-");
                }
                else {
                    var type = el.attr('type');
                    if (type == 'radio') {
                        var val = $('[name="' + v + '"]:checked').val();
                    }
                    else {
                        var val = el.val();
                    }
                }

                var check = val.split("-");
                for (var m = 0, limit2 = check.length; m < limit2; ++m) {
                    var t = check[m];
                    requiredvalues.push(t);
                }
            }
            else {
                continue;
            }
        }
    }

    for (var m = 0, limit2 = requiredvalues.length; m < limit2; ++m) {
        for (j = 0; j < values.length; j++) {
            if (parseFloat(values[j]) >= parseFloat(requiredvalues[m])) {
                return false;
            }
        }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* numeric comparisons not set of enumerated/multi-dropdown*/

/* answer equal to other one(s) */
jQuery.validator.addMethod("numeric_equalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    var values = value.split("-");
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            for (j = 0; j < values.length; j++) {
                if (parseFloat(values[j]) != parseFloat(v)) {
                    return false;
                }
            }
        }
        else {
///            if ($('[name="' + v + '"]').is(':visible')) {
            if ($('[name="' + v + '"]').length > 0) {
                if ($('[name="' + v + '"]').is(':radio')) {
                    var val = $('[name="' + v + '"]:checked').val();
                }
                else {
                    var val = $('[name="' + v + '"]').val();
                }
                if (!jQuery.isNumeric(val)) {
                    continue;
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) != parseFloat(val)) {
                        return false;
                    }
                }
            }
            else if ($('[name="' + v + '[]"]').length > 0) {
                var t = '' + $('[name="' + v + '[]"]').val();
                if (t.indexOf(",") > -1) { // multi-dropdown
                    var vals = t.split(",");
                }
                else { // checkboxes
                    var vals = t.split("-");
                }
                
                // more than one option selected, then not equal to numeric value
                if (vals.length > 1) {
                    return false;
                }
                var val = vals[0];
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) != parseFloat(val)) {
                        return false;
                    }
                }
            }            
            else {
                continue;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer not equal to other one(s) */
jQuery.validator.addMethod("numeric_notequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    //alert(value);
    var values = ('' + value).split(",");
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            for (j = 0; j < values.length; j++) {
                if (parseFloat(values[j]) == parseFloat(v)) {
                    return false;
                }
            }
        }
        else {
///            if ($('[name="' + v + '"]').is(':visible')) {
            if ($('[name="' + v + '"]').length > 0) {
                if ($('[name="' + v + '"]').is(':radio')) {
                    var val = $('[name="' + v + '"]:checked').val();
                }
                else {
                    var val = $('[name="' + v + '"]').val();
                }
                if (!jQuery.isNumeric(val)) {
                    continue;
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) == parseFloat(val)) {
                        return false;
                    }
                }
            }
            else if ($('[name="' + v + '[]"]').length > 0) {
                var t = '' + $('[name="' + v + '[]"]').val();
                if (t.indexOf(",") > -1) { // multi-dropdown
                    var vals = t.split(",");
                }
                else { // checkboxes
                    var vals = t.split("-");
                }
                
                // check each selected value                
                for (j = 0; j < values.length; j++) {
                    for (k = 0; k < vals.length; k++) {
                        if (parseFloat(values[j]) < parseFloat(vals[k])) {
                            return false;
                        }
                    }
                }
            } 
            else {
                continue;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* answer greater or equal to other one(s) */
jQuery.validator.addMethod("numeric_greaterequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    var values = value.split("-");
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            for (j = 0; j < values.length; j++) {
                if (parseFloat(values[j]) < parseFloat(v)) {
                    return false;
                }
            }
        }
        else {
///            if ($('[name="' + v + '"]').is(':visible')) {
            if ($('[name="' + v + '"]').length > 0) {
                if ($('[name="' + v + '"]').is(':radio')) {
                    var val = $('[name="' + v + '"]:checked').val();
                }
                else {
                    var val = $('[name="' + v + '"]').val();
                }
                if (!jQuery.isNumeric(val)) {
                    continue;
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) < parseFloat(val)) {
                        return false;
                    }
                }
            }
            else if ($('[name="' + v + '[]"]').length > 0) {
                var t = '' + $('[name="' + v + '[]"]').val();
                if (t.indexOf(",") > -1) { // multi-dropdown
                    var vals = t.split(",");
                }
                else { // checkboxes
                    var vals = t.split("-");
                }
                
                // more than one option selected, then get the highest value
                var val = "";
                if (vals.length > 1) {
                    vals = vals.sort(sortNumber); 
                    val = vals[vals.length-1];
                }
                else {
                    val = vals[0];
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) < parseFloat(val)) {
                        return false;
                    }
                }
            } 
            else {
                continue;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer greater than other one(s) */
jQuery.validator.addMethod("numeric_greater", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    var values = value.split("-");
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            for (j = 0; j < values.length; j++) {
                if (parseFloat(values[j]) <= parseFloat(v)) {
                    return false;
                }
            }
        }
        else {
///            if ($('[name="' + v + '"]').is(':visible')) {
            if ($('[name="' + v + '"]').length > 0) {
                if ($('[name="' + v + '"]').is(':radio')) {
                    var val = $('[name="' + v + '"]:checked').val();
                }
                else {
                    var val = $('[name="' + v + '"]').val();
                }
                if (!jQuery.isNumeric(val)) {
                    continue;
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) <= parseFloat(val)) {
                        return false;
                    }
                }
            }
            else if ($('[name="' + v + '[]"]').length > 0) {
                var t = '' + $('[name="' + v + '[]"]').val();
                if (t.indexOf(",") > -1) { // multi-dropdown
                    var vals = t.split(",");
                }
                else { // checkboxes
                    var vals = t.split("-");
                }
                
                // more than one option selected, then get the highest value
                var val = "";
                if (vals.length > 1) {
                    vals.sort(sortNumber); 
                    val = vals[vals.length-1];
                }
                else {
                    val = vals[0];
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) < parseFloat(val)) {
                        return false;
                    }
                }
            } 
            else {
                continue;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller or equal to other one(s) */
jQuery.validator.addMethod("numeric_smallerequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    var values = value.split("-");
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            for (j = 0; j < values.length; j++) {
                if (parseFloat(values[j]) > parseFloat(v)) {
                    return false;
                }
            }
        }
        else {
///            if ($('[name="' + v + '"]').is(':visible')) {
            if ($('[name="' + v + '"]').length > 0) {
                if ($('[name="' + v + '"]').is(':radio')) {
                    var val = $('[name="' + v + '"]:checked').val();
                }
                else {
                    var val = $('[name="' + v + '"]').val();
                }
                if (!jQuery.isNumeric(val)) {
                    continue;
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) > parseFloat(val)) {
                        return false;
                    }
                }
            }
            else if ($('[name="' + v + '[]"]').length > 0) {
                var t = '' + $('[name="' + v + '[]"]').val();
                if (t.indexOf(",") > -1) { // multi-dropdown
                    var vals = t.split(",");
                }
                else { // checkboxes
                    var vals = t.split("-");
                }
                
                // more than one option selected, then get the lowest value
                var val = "";
                if (vals.length > 1) {
                    vals = vals.sort(sortNumber); 
                    val = vals[0];
                }
                else {
                    val = vals[0];
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) > parseFloat(val)) {
                        return false;
                    }
                }
            } 
            else {
                continue;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller than other one(s) */
jQuery.validator.addMethod("numeric_smaller", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    var values = value.split("-");
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (jQuery.isNumeric(v)) {
            for (j = 0; j < values.length; j++) {
                if (parseFloat(values[j]) >= parseFloat(v)) {
                    return false;
                }
            }
        }
        else {
///            if ($('[name="' + v + '"]').is(':visible')) {
            if ($('[name="' + v + '"]').length > 0) {
                if ($('[name="' + v + '"]').is(':radio')) {
                    var val = $('[name="' + v + '"]:checked').val();
                }
                else {
                    var val = $('[name="' + v + '"]').val();
                }
                if (!jQuery.isNumeric(val)) {
                    continue;
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) >= parseFloat(val)) {
                        return false;
                    }
                }
            }
            else if ($('[name="' + v + '[]"]').length > 0) {
                var t = '' + $('[name="' + v + '[]"]').val();
                if (t.indexOf(",") > -1) { // multi-dropdown
                    var vals = t.split(",");
                }
                else { // checkboxes
                    var vals = t.split("-");
                }
                
                // more than one option selected, then get the lowest value
                var val = "";
                if (vals.length > 1) {
                    vals = vals.sort(sortNumber); 
                    val = vals[0];
                }
                else {
                    val = vals[0];
                }
                for (j = 0; j < values.length; j++) {
                    if (parseFloat(values[j]) >= parseFloat(val)) {
                        return false;
                    }
                }
            } 
            else {
                continue;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* string comparisons */

/* answer equal to other one(s) */
jQuery.validator.addMethod("string_equalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (v === "") {
            continue;
        }
///            if ($('[name="' + v + '"]').is(':visible')) {
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
            if (value !== val) {
                return false;
            }
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
            if (value !== val) {
                return false;
            }
        }
        else {
            if (value !== v) {
                return false;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer equal ignore case to other one(s) */
jQuery.validator.addMethod("string_equaltoignorecase", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (v === "") {
            continue;
        }

///            if ($('[name="' + v + '"]').is(':visible')) {
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
            if (value.toUpperCase() !== val.toUpperCase()) {
                return false;
            }
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
            if (value !== val) {
                return false;
            }
        }
        else {
            if (value !== v) {
                return false;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* answer not equal to other one(s) */
jQuery.validator.addMethod("string_notequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (v === "") {
            continue;
        }

///            if ($('[name="' + v + '"]').is(':visible')) {
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
            if (value === val) {
                return false;
            }
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
            if (value === val) {
                return false;
            }
        }
        else {
            if (value !== v) {
                return false;
            }
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer not equal to other one(s) */
jQuery.validator.addMethod("string_notequaltoignorecase", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if (v == "") {
            continue;
        }

///            if ($('[name="' + v + '"]').is(':visible')) {
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
            if (value.toUpperCase() === val.toUpperCase()) {
                return false;
            }
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
            if (value === val) {
                return false;
            }
        }
        else {
            if (value !== v) {
                return false;
            }
        }

        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));



/* date/datetime comparisons */

/* answer equal to other one(s) */
jQuery.validator.addMethod("datetime_equalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
///            if ($('[name="' + v + '"]').is(':visible')) {

        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }

        if (val == "") {
            continue;
        }

        var one = getMoment(value);
        var two = getMoment(val);

        if (one.isValid() == false) {
            return false;
        }
        if (two.isValid() == false) {
            return false;
        }
        if (one.isSame(two, 'second') == false) {
            return false;
        }

        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer not equal to other one(s) */
jQuery.validator.addMethod("datetime_notequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");

        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }

        if (val == "") {
            continue;
        }
        var one = getMoment(value);
        var two = getMoment(val);

        if (one.isValid() == false) {
            return false;
        }
        if (two.isValid() == false) {
            return false;
        }
        if (one.isSame(two, 'second')) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* answer greater or equal to other one(s) */
jQuery.validator.addMethod("datetime_greaterequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }

        if (val == "") {
            continue;
        }
        var one = getMoment(value);
        var two = getMoment(val);

        if (one.isValid() == false) {
            return false;
        }
        if (two.isValid() == false) {
            return false;
        }
        if (one.isBefore(two, 'second') == true) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer greater than other one(s) */
jQuery.validator.addMethod("datetime_greater", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }

        if (val == "") {
            continue;
        }
        var one = getMoment(value);
        var two = getMoment(val);

        if (one.isValid() == false) {
            return false;
        }
        if (two.isValid() == false) {
            return false;
        }
        if (one.isAfter(two, 'second') == false) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller or equal to other one(s) */
jQuery.validator.addMethod("datetime_smallerequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        var one = getMoment(value);
        var two = getMoment(val);

        if (one.isValid() == false) {
            return false;
        }
        if (two.isValid() == false) {
            return false;
        }
        if (one.isAfter(two, 'second') == true) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller than other one(s) */
jQuery.validator.addMethod("datetime_smaller", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        var one = getMoment(value);
        var two = getMoment(val);

        if (one.isValid() == false) {
            return false;
        }
        if (two.isValid() == false) {
            return false;
        }
        if (one.isBefore(two, 'second') == false) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* time comparisons */

/* answer equal to other one(s) */
jQuery.validator.addMethod("time_equalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
///            if ($('[name="' + v + '"]').is(':visible')) {
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }

        var temp = moment().format("YYYY-MM-DD");
        value = temp.concat(" ", value);
        var one = getMoment(value);

        if (one.isValid() == false) {
            return false;
        }

        val = temp.concat(" ", val);
        var two = getMoment(val);

        if (two.isValid() == false) {
            return false;
        }

        if (one.isSame(two, 'second') == false) {
            return false;
        }

        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer not equal to other one(s) */
jQuery.validator.addMethod("time_notequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        var temp = moment().format("YYYY-MM-DD");
        value = temp.concat(" ", value);
        var one = getMoment(value);

        if (one.isValid() == false) {
            return false;
        }

        val = temp.concat(" ", val);
        var two = getMoment(val);

        if (two.isValid() == false) {
            return false;
        }

        if (one.isSame(two, 'second')) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


/* answer greater or equal to other one(s) */
jQuery.validator.addMethod("time_greaterequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        
        // check for 12am, change to 00 for accurate comparison
        if (value.startsWith("12:") && value.toLowerCase().endsWith("am")) {
            var arr = value.split(":");
            value = "00:" + arr.splice(1,1).join(":");
        }
        else if (value.startsWith("12") && value.toLowerCase().endsWith("pm")) {
            var arr = value.split(":");
            //value = "00:" + arr[1];
            value = "00:" + arr.splice(1,1).join(":");
        }
        
        // value already has a date!
        if (value.indexOf("/") > 0) {
            
        }
        else {
            var temp = moment().format("YYYY-MM-DD");
            value = temp.concat(" ", value);
        }
        var one = getMoment(value);

        if (one.isValid() == false) {
            return false;
        }

        // check for 12am, change to 00 for accurate comparison
        if (val.startsWith("12:") && val.toLowerCase().endsWith("am")) {
            var arr = val.split(":");
            val = "00:" + arr.splice(1,1).join(":");
        }
        else if (val.startsWith("12") && val.toLowerCase().endsWith("pm")) {
            var arr = val.split(":");
            //value = "00:" + arr[1];
            val = "00:" + arr.splice(1,1).join(":");
        }

        // val already has a date!
        if (val.indexOf("/") > 0) {
            //alert(value + "---" + val);
        }
        else {
            var temp1 = moment().format("YYYY-MM-DD");
            val = temp1.concat(" ", val);
        }
        
        var two = getMoment(val);

        if (two.isValid() == false) {
            return false;
        }
        if (one.isBefore(two, 'second') == true) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer greater than other one(s) */
jQuery.validator.addMethod("time_greater", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        var temp = moment().format("YYYY-MM-DD");
        value = temp.concat(" ", value);
        var one = getMoment(value);

        if (one.isValid() == false) {
            return false;
        }

        val = temp.concat(" ", val);
        var two = getMoment(val);

        if (two.isValid() == false) {
            return false;
        }

        if (one.isAfter(two, 'second') == false) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller or equal to other one(s) */
jQuery.validator.addMethod("time_smallerequalto", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        var temp = moment().format("YYYY-MM-DD");
        value = temp.concat(" ", value);
        var one = getMoment(value);

        if (one.isValid() == false) {
            return false;
        }

        val = temp.concat(" ", val);
        var two = getMoment(val);

        if (two.isValid() == false) {
            return false;
        }
        if (one.isAfter(two, 'second') == true) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));

/* answer smaller than other one(s) */
jQuery.validator.addMethod("time_smaller", function(value, element, args) {
    if (value == "" || value === undefined) {
        return true;
    }
    // ignore if empty
    if (args == '') {
        return true;
    }
    for (var i = 0, limit = args.length; i < limit; ++i) {
        var v = args[i].replace("'", "").replace("'", "");
        if ($('[name="' + v + '"]').length > 0) {
            var val = $('[name="' + v + '"]').val();
        }
        else if ($('[name="' + v + '[]"]').length > 0) {
            var val = $('[name="' + v + '[]"]').val();
        }
        else {
            val = v;
        }
        if (val == "") {
            continue;
        }
        var temp = moment().format("YYYY-MM-DD");
        value = temp.concat(" ", value);
        var one = getMoment(value);

        if (one.isValid() == false) {
            return false;
        }

        val = temp.concat(" ", val);
        var two = getMoment(val);

        if (two.isValid() == false) {
            return false;
        }
        if (one.isBefore(two, 'second') == false) {
            return false;
        }
        //     }
    }
    return true;
}, jQuery.format("Please give the same answer to the questions."));


// http://stackoverflow.com/questions/3390930/any-way-to-make-jquery-inarray-case-insensitive
(function($) {
    $.extend({
        // Case insensative $.inArray (http://api.jquery.com/jquery.inarray/)
        // $.inArrayIn(value, array [, fromIndex])
        //  value (type: String)
        //    The value to search for
        //  array (type: Array)
        //    An array through which to search.
        //  fromIndex (type: Number)
        //    The index of the array at which to begin the search.
        //    The default is 0, which will search the whole array.
        inArrayIn: function(elem, arr, i) {
            // not looking for a string anyways, use default method
            if (typeof elem !== 'string') {
                return $.inArray.apply(this, arguments);
            }
            // confirm array is populated
            if (arr) {
                var len = arr.length;
                i = i ? (i < 0 ? Math.max(0, len + i) : i) : 0;
                elem = elem.toLowerCase();
                for (; i < len; i++) {
                    if (i in arr && arr[i].toLowerCase() == elem) {
                        return i;
                    }
                }
            }
            // stick with inArray/indexOf and return -1 on no match
            return -1;
        }
    });
})(jQuery);

function getMoment(value) {
    var add = 0;
    if (value.indexOf(" PM") > -1) {
        add = 1;
        value = value.replace(" PM", "");
    }
    value = value.replace(" AM", "");
    var one = moment(value);
    if (add == 1) {
        one.add(12, 'hours');
    }
    return one;
}

// http://stackoverflow.com/questions/280634/endswith-in-javascript
function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}

// http://stackoverflow.com/questions/1063007/how-to-sort-an-array-of-integers-correctly
function sortNumber(a, b) {
    return a - b;
}