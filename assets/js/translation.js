$(document).ready(function () {
    $(document).on("click", ".update", function () {
        $('#translate_modal').find('#file_name').val($(this).data('file'))
        $('#translate_modal').find('#key_name').val($(this).data('key'))

        $(this).closest('tr').find('td').each(function (index, value) {
            $('#translate_modal').find('textarea').eq(index).val($(this).html())
        })
    })
})