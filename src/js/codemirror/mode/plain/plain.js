CodeMirror.defineMode("plain", function() {
  function words(str) {
    var obj = {}, words = str.split(" ");
    for (var i = 0; i < words.length; ++i) obj[words[i]] = true;
    return obj;
  }
  var keywords = words();
  var atoms = {"null": true};

  var isOperatorChar = /[+\-*&%=<>!?|\/]/;

  function tokenBase(stream, state) {
    
  }

  function tokenString(quote) {
    
  }

  function tokenComment(stream, state) {
    
  }

  // Interface

  return {
    startState: function() {
      return {tokenize: null};
    },

    token: function(stream, state) {
      if (stream.eatSpace()) return null;
      var style = (state.tokenize || tokenBase)(stream, state);
      return style;
    },
  };
});

CodeMirror.defineMIME("text/x-plain", "plain");
