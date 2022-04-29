jQuery(document).ready(function ($) {
    $('form#removeTable').submit(function (e) {
        e.preventDefault();
        var button = $(this).find('button'),
            $form = $(this);
            tableCount = $form.siblings('.admin-table-request').find('tbody').children('tr').length;
        $.ajax({
            type: 'POST',
            url: deleteTable.ajaxTable,
            data: {
                count: tableCount,
                action: deleteTable.action,
            },
            beforeSend : function ( xhr ) {
                button.text('Удаление...');
            },
            success: function (response) {
                if(response.success){
                    window.location.reload();
                } else {
                    alert('Таблица и так пустая!');
                    button.text('УДАЛИТЬ ДАННЫЕ');
                }
            },
            error: function (request, txtstatus, errorThrown) {
                console.log(request);
                console.log(txtstatus);
                console.log(errorThrown);
                button.text('Error');
                button.css('background-color','#ff0000');
                setTimeout(function () {
                    button.text('УДАЛИТЬ ДАННЫЕ');
                    button.css('background-color','initial');
                },2000)
            }
        });
    });
});