(function() {
    function forEach(arr, f) {
        for (var i = 0, e = arr.length; i < e; ++i)
            f(arr[i]);
    }

    function arrayContains(arr, item) {
        if (!Array.prototype.indexOf) {
            var i = arr.length;
            while (i--) {
                if (arr[i] === item) {
                    return true;
                }
            }
            return false;
        }
        return arr.indexOf(item) != -1;
    }

    function scriptHint(editor, _keywords, getToken) {
        // Find the token at the cursor
        var cur = editor.getCursor(), token = getToken(editor, cur), tprop = token;
        // If it's not a 'word-style' token, ignore the token.

        if (!/^[\w$_]*$/.test(token.string)) {
            token = tprop = {start: cur.ch, end: cur.ch, string: "", state: token.state,
                className: token.string == ":" ? "nubis-type" : null};
        }

        if (!context)
            var context = [];
        context.push(tprop);

        var completionList = getCompletions(token, context);
        completionList = completionList.sort();
        //prevent autocomplete for last word, instead show dropdown with one word
        if (completionList.length == 1) {
            completionList.push(" ");
        }

        return {list: completionList,
            from: CodeMirror.Pos(cur.line, token.start),
            to: CodeMirror.Pos(cur.line, token.end)};
    }

    function nubisHint(editor) {
        return scriptHint(editor, nubisKeywordsU, function(e, cur) {
            return e.getTokenAt(cur);
        });
    }
    CodeMirror.nubisHint = nubisHint; // deprecated
    CodeMirror.registerHelper("hint", "nubis", nubisHint);

    var nubisKeywords = "";
    var nubisKeywordsL = nubisKeywords.split(" ");
    var nubisKeywordsU = nubisKeywords.toUpperCase().split(" ");

    var variables = [];
    $.getJSON('index.php?p=sysadmin.autocompletecodemirror&ajax=smsajax', function(data) {
        $.each(data, function(key, val) {
            variables.push(val);
        });
    });
    var nubisBuiltins = "cardinal card group subgroup endgroup endsubgroup empty nonresponse dk rf do enddo endif for and array if then elseif else in mod not or to inline inspect fill while endwhile exitfor exitwhile"
            + "";
    var nubisBuiltinsL = nubisBuiltins.split(" ");
    var nubisBuiltinsU = nubisBuiltins.toUpperCase().split(" ");

    function getCompletions(token, context) {
        var found = [], start = token.string.toLowerCase();
        function maybeAdd(str) {
            if (str.toLowerCase().indexOf(start) == 0 && !arrayContains(found, str.toLowerCase()))
                found.push(str.toLowerCase());
        }

        function gatherCompletions(_obj) {
            forEach(nubisBuiltinsL, maybeAdd);
            forEach(nubisBuiltinsU, maybeAdd);
            forEach(nubisKeywordsL, maybeAdd);
            forEach(nubisKeywordsU, maybeAdd);
            forEach(variables, maybeAdd);
        }

        if (context) {
            // If this is a property, see if it belongs to some object we can
            // find in the current environment.
            var obj = context.pop(), base;

            if (obj.type == "variable")
                base = obj.string;
            else if (obj.type == "variable-3")
                base = ":" + obj.string;

            while (base != null && context.length)
                base = base[context.pop().string];
            if (base != null)
                gatherCompletions(base);
        }
        return found;
    }
})();
