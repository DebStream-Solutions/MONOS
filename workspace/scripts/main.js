function hideLoad() {
    $("#loading").animate({ top: '-50%', opacity: 0 }, 1000, function() {
        $(this).fadeOut(1000);
    });
}

$("#net_chart").ready(() => {
    setTimeout(hideLoad, 1000);
});


function onLoad() {
    if (window.loaded) {
        loaded();
    }

    $(".sidebar-content > div").click(function() {
        if ($(this).children(".title.up").length > 0) {
            $(this).children(".roll").slideToggle(200, () => {
                $(this).children(".title").removeClass("up");
            });
        } else {
            $(this).children(".roll").slideToggle(200);
            $(this).children(".title").addClass("up");
            $(this).children(".roll").css("display", "flex");
        }
    });
}


$(document).ready(onLoad);