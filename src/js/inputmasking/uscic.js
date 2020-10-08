
/* 
 ------------------------------------------------------------------------
 Copyright (C) 2014 Bart Orriens, Albert Weerman
 
 This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.
 
 This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
 
 You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 ------------------------------------------------------------------------
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery', './jquery.inputmask'], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {

    $.extend($.inputmask.defaults, {
        onKeyValidation: function (result) {
            if (result === false) {
                if ($.isFunction(window.inputmaskCallbackError)) {
                    inputmaskCallbackError();
                }
            }
        }
    });

    $.extend($.inputmask.defaults.definitions, {
        'C': {
            validator: "[1-9]",
            cardinality: 1,
            definitionSymbol: "*"
        },
        'N': {
            validator: "[0-9]",
            cardinality: 1,
            definitionSymbol: "*"
        },
        'Z': {
            validator: "[AC-HJ-KMNP-RTUVWXYac-hj-kmnp-rtuvwxy]", // S, L, O, I, B, Z
            cardinality: 1,
            definitionSymbol: "*",
            casing: "upper"
        },
        'B': {
            validator: "[ACDEFGHJKMNPQRTUVWXYacdefghjkmnpqrtuvwxy0-9]",
            cardinality: 1,
            //definitionSymbol: "*",
            casing: "upper"
        },
    });

    $.extend($.inputmask.defaults.aliases, {
        "usphone": {
            mask: "+1 (999) 999-9999",
            greedy: false
        },
        "medicare": {
            mask: "CZBN-ZBN-ZZNN",
            greedy: false
        },
        "social": {
            mask: "XXX-XXX-NNNN",
            greedy: false
        },
        "eurocurrency": {
            mask: "€ 9[99[.999[.999[,99]]]]",
            greedy: false
        },
        "emailshort": {
            mask: "*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}.*{2,6}",
            greedy: false
        },
        "uscurrency": {
            alias: 'numeric',
            groupSeparator: ',',
            autoGroup: true,
            digits: 0,
            digitsOptional: false,
            radixPoint: '.'/*,
             autoUnmask: true,
             removeMaskOnSubmit: true,
             onUnMask: function (maskedValue, unmaskedValue, opts) {
             //alert(unmaskedValue);
             //var processValue = maskedValue.replace(opts.prefix, "");
             //processValue = processValue.replace(opts.suffix, "");
             var processValue = maskedValue.replace(new RegExp($.inputmask.escapeRegex.call(this, opts.groupSeparator), "g"), "");
             processValue = processValue.replace($.inputmask.escapeRegex.call(this, opts.radixPoint), ".");
             //alert(processValue);
             return processValue;
             }*/
        }
    });
    return $.fn.inputmask;
}));
