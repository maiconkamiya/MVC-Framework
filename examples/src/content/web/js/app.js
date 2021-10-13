var al = {
    dialog: function(tipo, titulo, descricao){
        alert(descricao);
    },
    confirm: function(){}
}

var fn = {
    load:{
        cnpj: function(t, el){
            var table = $(t).parents('[id^="table-"]');
            var text = table.find(`[name="${el}"]`).val();
            text=text.replace(/([-.*+?^=!:${}()|\[\]\/\\])/g,'');
            $.get('http://localhost:82/aplicacao/result/cliente/receita/' + text, function(r){
                table.find('[name="razao"]').val(r.nome);
                table.find('[name="fantasia"]').val(r.fantasia);
                table.find('[name="endereco"]').val(r.logradouro);
                table.find('[name="numero"]').val(r.numero);
                table.find('[name="bairro"]').val(r.bairro);
                table.find('[name="email"]').val(r.email);
                table.find('[name="cep"]').val(r.cep);
                table.find('[name="telefone"]').val(r.telefone);
                table.find('[name="municipio"]').val(r.ibge.cidade);
                table.find('[name="uf"]').val(r.ibge.uf);
                table.find('[name="ibge"]').val(r.ibge.ibge);
            },'JSON');
        },
        cep: function(t, el){
            var table = $(t).parents('[id^="table-"]');
            var text = table.find(`[name="${el}"]`).val();
            text=text.replace(/([-.*+?^=!:${}()|\[\]\/\\])/g,'');
            $.get(window.Area + 'emissor/cep/consulta/' + text, function(r){
                if (('error' in r)==false){
                    table.find('[name="endereco"]').val(r.endereco);
                    table.find('[name="bairro"]').val(r.bairro);
                    table.find('[name="municipio"]').val(r.municipio);
                    table.find('[name="uf"]').val(r.uf);
                    table.find('[name="ibge"]').val(r.ibge);
                } else {
                    al.dialog('warning','Pesquisa por CEP', r.error)
                }
            },'JSON');
        }
    },
    modal: function(uri, title, size){

        $.get(window.Area + uri, function (h) {
            var id = new Date().getTime();
            var sz = (size ? 'modal-' + size : 'modal-md');
            var html = '<div class="modal fade" id="modal-' + id + '" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">'
                + '<div class="modal-dialog ' + sz + '" role="document">'
                + '<div class="modal-content">'
                + '<div class="modal-header">'
                + '<h5 class="modal-title" id="staticBackdropLabel">' + title + '</h5>'
                + '<button type="button" class="btn-close close" dir="' + id + '"  data-bs-dismiss="modal" aria-label="Close"></button>'
                + '</div>'
                + h
                + '</div>'
                + '</div>'
                + '</div>';

            $('body').append(html);

            $('#modal-' + id).modal('show');
        });

    },
    modalClose: function (id) {
        setTimeout(function () {
            $('body').find('div#modal-' + id).remove();
        }, 500);
    }
};

$(document).on('click', '.modal .close', function () {
    var id = $(this).attr('dir');

    fn.modalClose(id);
});

$(document).on('click', '[dialog="open"]', function () {
    var uri = $(this).attr('uri');
    var title = $(this).attr('title');
    var size = $(this).attr('size');

    fn.modal(uri,title,size);
});

var render = function(){
    var hash = window.location.hash;
    hash = hash.replace('#','');
    //hash = hash == '' ? window.Init : hash;

    if (hash!=''){
        $.get(window.Area + hash, function(response){
            $('#main').empty().html(response);
        });
    }
};

window.onhashchange = render();
window.addEventListener('popstate', function(event)
{
    if(window.location.hash) {
        render();
    }
});

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}