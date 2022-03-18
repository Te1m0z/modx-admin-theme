$(document).ready(function () {

    $('form .items-container').each(function () {
        $(this).on('click', 'button.btn-danger', function (event) {
            $(this).parent().fadeOut(500, function () {
                $(this).remove();
            });
            event.stopPropagation();
        });
    });

    $('form .items-container .btn-outline-success').each(function () {
        $(this).click(function () {
            console.log(this)
            $(this).parent().parent().find('ul').append(`
                        <li class="">
                            <div class="input-group">
                                <span class="form-control d-inline-block bg-light">Объём:</span>
                                <input type="number" class="form-control" required/>
                            </div>
                            <div class="input-group">
                                <span class="form-control d-inline-block bg-light">ЕС:</span>
                                <input type="number" class="form-control" required/>
                            </div>
                            <div class="input-group">
                                <span class="form-control d-inline-block bg-light">рН:</span>
                                <input type="number" class="form-control" required/>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm rounded-circle mt-1">&#10006;</button>
                        </li>
            `);
        });
    });

    $("input[type='text']").keypress(function (event) {
        if (event.which === 13) {

            var todoText = $(this).val();
            $(this).val("");

            $("ul").append("<li><span><i class='fa fa-trash'></i></span> " + todoText + "</li>")
        }
    });

    $(".fa-plus").click(function () {
        $("input[type='text']").fadeToggle();
    });
});