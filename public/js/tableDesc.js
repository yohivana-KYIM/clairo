$(document).ready(function() {
    var $table = $('#titre');

    $('#titre').bootstrapTable('destroy');

    if (!$table.hasClass('table-initialized')) {
        $table.bootstrapTable({
            columns: [
                {
                    field: 'id',
                    title: 'ID',
                    visible: false
                }
            ],
            sortOrder: 'desc',
            sortName: 'id',
        });

        $table.addClass('table-initialized');
    }
});