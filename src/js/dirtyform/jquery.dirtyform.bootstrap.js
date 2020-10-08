/*!
Bootstrap modal dialog (for jQuery Dirty Forms) | v1.2.0 | github.com/snikch/jquery.dirtyforms
(c) 2015 Shad Storhaug
License MIT
*/

// Support for UMD: https://github.com/umdjs/umd/blob/master/jqueryPluginCommonjs.js
// This allows for tools such as Browserify to compose the components together into a single HTTP request.
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}

(function ($) {
    var exclamationGlyphicon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ';
    var title;

    $.DirtyForms.dialog = {        
        // Custom properties and methods to allow overriding (may differ per dialog)
        title: exclamationGlyphicon + $.DirtyForms.dialog.title,
        continueButtonClass: 'dirty-continue',
        continueButtonText: 'Leave This Page',
        cancelButtonClass: 'dirty-cancel',
        cancelButtonText: 'Stay Here',
        dialogID: $.DirtyForms.dialog.dialogID,
        titleID: 'dirty-title',
        messsageClass: 'dirty-message',
        preMessageText: '',
        postMessageText: '',
        replaceText: true,

        // Typical Dirty Forms Properties and Methods
        open: function (choice, message) {
            
            // hard coded hack Nubis, currently passing parameters through function call is not working
            this.continueButtonClass = $.DirtyForms.dialog.continueButtonClass;
            this.continueButtonText = $.DirtyForms.dialog.continueButtonText;
            this.cancelButtonClass = $.DirtyForms.dialog.cancelButtonClass;
            this.cancelButtonText = $.DirtyForms.dialog.cancelButtonText;
            this.dialogID = $.DirtyForms.dialog.dialogID;
            this.title = $.DirtyForms.dialog.title;
            this.titleID = $.DirtyForms.dialog.titleID;

            // Look for a pre-existing element with the dialogID.
            var $dialog = $('#' + this.dialogID);
            
            // If the user already added a dialog with this ID, skip doing it here
            if ($dialog.length === 0) {
                // NOTE: Buttons don't have the ignore class because Bootstrap 3 isn't compatible
                // with old versions of jQuery that don't properly cancel the click events.
                $dialog =
                    $('<div id="' + this.dialogID + '" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="' + this.titleID + '">' +
                        '<div class="modal-dialog" role="document">' +
                            '<div class="modal-content panel-danger">' +
                                '<div class="modal-header panel-heading">' +
                                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                    '<h3 class="modal-title" id="' + this.titleID + '"></h3>' +
                                '</div>' +
                                '<div class="modal-body panel-body ' + this.messsageClass + '"></div>' +
                                '<div class="modal-footer panel-footer">' +
                                    '<button type="button" class="' + this.continueButtonClass + ' btn btn-danger" data-dismiss="modal"></button>' +
                                    '<button type="button" class="' + this.cancelButtonClass + ' btn btn-default" data-dismiss="modal"></button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>');

                // Append to the body so we can capture DOM events.
                // Flag the dialog for later removal.
                $('body').append($dialog)
                         .data('df-dialog-appended', true);
            }

            if (this.replaceText) {
                // Replace the text in the dialog (whether it is external or not).
                $dialog.find('#' + this.titleID).html(this.title);
                $dialog.find('.' + this.messsageClass).html(this.preMessageText + message + this.postMessageText);
                $dialog.find('.' + this.continueButtonClass).html(this.continueButtonText);
                $dialog.find('.' + this.cancelButtonClass).html(this.cancelButtonText);
            }

            // Bind the events
            choice.bindEscKey = false;

            var onContinueClick = function () {
                choice.continue = $.DirtyForms.choiceContinue = true;
            };
            var onHidden = function (e) {
                var commit = choice.isDF1 ? $.DirtyForms.choiceCommit : choice.commit;
                commit(e);
                if ($('body').data('df-dialog-appended') === true) {
                    $dialog.remove();
                }
            };
            // NOTE: Bootstrap 3 requires jQuery 1.9, so we can use on and off here.
            $dialog.find('.' + this.continueButtonClass).off('click', onContinueClick).on('click', onContinueClick);
            $dialog.off('hidden.bs.modal', onHidden).on('hidden.bs.modal', onHidden);

            // Show the dialog
            $dialog.modal({ show: true });
        },

        // Support for Dirty Forms < 2.0
        fire: function (message, title) {
            this.title = exclamationGlyphicon + title;
            this.open({ isDF1: true }, message);
        },

        // Support for Dirty Forms < 1.2
        bind: function () {
        },
        stash: function () {
            return false;
        },
        refire: function () {
            return false;
        },
        selector: 'no-op',
    };

}));
