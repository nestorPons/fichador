$(function() {
    $('body').on('submit', '#loginUsuario ', function(e) {

        var hash = sha256.create();
        hash.update($(this).find('[type="password"]').val());

        $(this).find('[type="password"]').val(sha256.hmac('key', $(this).find('[type="password"]').val()))
        console.log(data)
    })
})