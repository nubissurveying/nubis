CodeMirror.defineMode("nubis", function() {
  function words(str) {
    var obj = {}, words = str.split(" ");
    for (var i = 0; i < words.length; ++i) {
        obj[words[i]] = true;
        obj[words[i].toUpperCase()] = true;
    }
    return obj;
  }

  var keywords = words("cardinal card group subgroup endgroup endsubgroup empty nonresponse dk rf do enddo endif for and array if then elseif else in mod not or to inline inspect inspectsection fill while endwhile exitfor exitwhile");
  var atoms = {"null": true};

  var isOperatorChar = /[+\-*&%=<>!?|\/]/i;

  function tokenBase(stream, state) {
    var ch = stream.next();
    if (ch == "#" && state.startOfLine) {
      stream.skipToEnd();
      return "meta";
    }
    if (ch == '"' || ch == "'") {
      state.tokenize = tokenString(ch);
      return state.tokenize(stream, state);
    }
    if (ch == "(" && stream.eat("*")) {
      state.tokenize = tokenComment;
      return tokenComment(stream, state);
    }
    if (/[\[\]{}\(\),;\:\.]/i.test(ch)) {
      return null;
    }
    if (/\d/i.test(ch)) {
      stream.eatWhile(/[\w\.]/i);
      return "number";
    }
    if (ch == "/") {
      if (stream.eat("/")) {
        stream.skipToEnd();
        return "comment";
      }
    }
    if (isOperatorChar.test(ch)) {
      stream.eatWhile(isOperatorChar);
      return "operator";
    }
    stream.eatWhile(/[\w\$_]/i);
    var cur = stream.current();
    if (keywords.propertyIsEnumerable(cur)) return "keyword";
    if (atoms.propertyIsEnumerable(cur)) return "atom";
    return "variable";
  }

  function tokenString(quote) {
    return function(stream, state) {
      var escaped = false, next, end = false;
      while ((next = stream.next()) != null) {
        if (next == quote && !escaped) {end = true; break;}
        escaped = !escaped && next == "\\";
      }
      if (end || !escaped) state.tokenize = null;
      return "string";
    };
  }

  function tokenComment(stream, state) {
    var maybeEnd = false, ch;
    while (ch = stream.next()) {
      if (ch == ")" && maybeEnd) {
        state.tokenize = null;
        break;
      }
      maybeEnd = (ch == "*");
    }
    return "comment";
  }

  // Interface

  return {
    startState: function() {
      return {tokenize: null};
    },

    token: function(stream, state) {
      if (stream.eatSpace()) return null;
      var style = (state.tokenize || tokenBase)(stream, state);
      if (style == "comment" || style == "meta") return style;
      return style;
    },

    electricChars: "{}"
  };
});

CodeMirror.defineMIME("text/x-nubis", "nubis");
