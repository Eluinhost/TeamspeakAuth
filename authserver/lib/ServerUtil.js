var fs = require('fs'),
    util = require('util'),
    mime = require('mime'),
    chance = require('chance').Chance(),
    jQuery = require('jquery-deferred');

module.exports = {
    /**
     * Takes the file at the location and returns the base64 encoded image string back
     * @param {String} src the filepath
     * @returns {String} the base64 encoded version of the image
     */
    base64Image: function(src) {
        var data = fs.readFileSync(src).toString("base64");
        return util.format("data:%s;base64,%s", mime.lookup(src), data);
    },

    /**
     * Returns a promise that resolves after the given duration
     * @param {int} duration the ms to sleep for
     * @returns {Deferred} A promise that will resolve after the amount of ms
     */
    sleep: function(duration) {
        var deferred = new jQuery.Deferred();
        setTimeout(
            function() {
                deferred.resolve();
            },
            duration
        );
        return deferred.promise();
    },

    /**
     * Generate a random hash of the length supplied, all upper case
     * @param {int} amount The length of the code to create
     * @returns {String} the generated code
     */
    generateCode: function(amount) {
        return chance.hash({length: amount, casing: 'upper'});
    }
};