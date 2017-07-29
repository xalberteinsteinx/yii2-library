/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * This script generates aliases from title.
 */

$(document).ready(function () {

    var generateAliasBtn = $("button#generate-alias-button");
    var title = $("#title-input");

    generateAliasBtn.on('click', function () {

        $.ajax({
            url: generateAliasBtn.attr('data-url'),
            data: {'title': title.val()},
            success: function (result) {
                $("#alias-input").val(result);
            }
        });
    });

});