$(document).ready(function () {
    $(document).on('change', '[name=streamer]', function () {
        console.log($(this).val())
        const streamerName = $(this).val();
        localStorage.setItem('streamerName', streamerName);
        // console.log(streamerName);
        $("[data-streamer-name]").attr('data-streamer-name', streamerName);
    });

    $(document).on('click', "[data-streamer-name]", function (e) {
        e.preventDefault();
        // const streamerName = localStorage.getItem('streamerName');
        const streamerName = $(this).data("streamer-name");
        const href = $(this).prop('href') + '?streamer=' + streamerName;
        // console.log(location);
        location.href = href;
    });

    $('[name=streamer]').trigger('change');
});