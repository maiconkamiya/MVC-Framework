var al = {
    dialog: function(tipo, titulo, descricao){
        alert(descricao);
    },
    confirm: function(){}
};

var spn = {
    show: function(text){
        $('.spin').css({'display':'block'});
        $('.spin .caption').text(text);
    },
    hide: function(){
        $('.spin').css({'display':'none'});
    }
};

var calc = {};

var fn = {
    load:{
        cnpj: function(t, el){
            var table = $(t).parents('[id^="table-"]');
            var text = table.find(`[name="${el}"]`).val();
            text=text.replace(/([-.*+?^=!:${}()|\[\]\/\\])/g,'');
            $.get(window.Area + 'criativaReceita/consulta/cnpj/' + text, function(r){
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
            $.get(window.Area + 'criativaIBGE/consulta/cep/' + text, function(r){
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
        },
        rotina: function(){
            $.get(window.Area + 'criativaRoutine/routine/getlist', function(r){
                var code = "";

                var item = template.rotina.item;
                var subitem = template.rotina.subitem;
                var sub = template.rotina.sub;

                $.each(r, function(i,v){
                    if (v.sub.length > 0 ||  v.dir != ''){

                        var tItem = item;

                        tItem = tItem.replace('[nome]', v.nome);
                        tItem = tItem.replace('[icon]', v.icon);
                        tItem = tItem.replace('[class]', (v.sub.length > 0 ? 'sidenav-toggle' : ''));
                        tItem = tItem.replace('[attr]', (v.dir != "" ? (v.dialog == 'S' ? 'dialog="open" uri="'+ v.dir +'" size="'+ v.dsize +'" title="'+ v.nome +'"' : '') : ''));
                        tItem = tItem.replace('[uri]', (v.dir != "" ? (v.dialog == 'S' ? '' : '#/'+ v.dir ) : '#'));
                        tItem = tItem.replace('[sub]', (v.sub.length > 0 ? sub : ''));

                        var lSubItem = "";

                        $.each(v.sub, function(ir, vr){

                            var tSubItem = subitem;

                            tSubItem = tSubItem.replace('[nome]', vr.nome);
                            tSubItem = tSubItem.replace('[icon]', (vr.icon != '' ? '<' : ''));
                            tSubItem = tSubItem.replace('[class]', '');
                            tSubItem = tSubItem.replace('[attr]', (vr.dir != "" ? (vr.dialog == 'S' ? 'dialog="open" uri="'+ vr.dir +'" size="'+ vr.dsize +'" title="'+ vr.nome +'"' : '') : ''));
                            tSubItem = tSubItem.replace('[uri]', (vr.dir != "" ? (vr.dialog == 'S' ? 'javascript:;' : '#/'+ vr.dir ) : '#'));

                            lSubItem += tSubItem;
                        });

                        tItem = tItem.replace('[subitem]', lSubItem);
                        code += tItem;
                        /*
                         code += `<li class="nav-item ${}">`;
                         code += `<a class="nav-link dropdown-toggle" ${v.sub.length > 0 ? 'id="raiz-'+v.codrotina+'" data-bs-toggle="dropdown" aria-expanded="false"' : ''} ${(v.dir != "" ? (v.dialog == 'S' ? 'dialog="open" uri="'+ v.dir +'" size="'+ v.dsize +'" title="'+ v.nome +'"' : 'href="#/'+ v.dir +'"') : 'href="#"')}>${v.nome}</a>`;
                         if(v.sub.length > 0 && v.dir == ''){
                         code += `<ul class="dropdown-menu" aria-labelledby="raiz-${v.codrotina}">`;
                         $.each(v.sub, function(ir, vr){
                         code += `<li><a class="dropdown-item" ${(vr.dir != "" ? (vr.dialog == 'S' ? 'dialog="open" uri="'+ vr.dir +'" size="'+ vr.dsize +'" title="'+ vr.nome +'"' : 'href="#/'+ vr.dir +'"') : '')}>${vr.nome}</a></li>`;
                         });
                         code += `</ul>`;
                         }
                         code += `</li>`;
                        */
                    }
                });
                $('#list-rotina').html(code);
            },'JSON');
        },
        blur: function(uri, t, el, cl){
            console.log('blur', this);
            $.post(window.Area + uri, t, function(r){
                if (r == null){
                    $(el).find('input.' + cl).val('');
                } else {
                    $.each(r, function(i,v){
                        var ip = $(el).find('input[name="' + i + '"].' + cl);
                        ip.val(v);
                    });
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
                + '<button type="button" class="close" dir="modal-' + id + '" data-dismiss="modal" aria-label="Close">x</button>'
                + '</div>'
                + h
                + '</div>'
                + '</div>'
                + '</div>';

            $('body').append(html);

            $('#modal-' + id).modal({
                keyboard: false,
                backdrop: false
            });
        });

    },
    modalClose: function (id) {
        setTimeout(function () {
            $('body').find('div#' + id).remove();
        }, 500);
    },
    getSizeMain: function(){
        var main = $('#main');
        var container = main.parent('div.container-fluid');
        if (container.length > 0){
            return container[0].clientHeight
        } else {
            return null;
        }
    }
};

jQuery.ajaxSetup({
    beforeSend: function () {
    },
    success: function () {
    },
    complete: function (e) {
        if (e.status == '401'){
            location = "criativaUser/sessao";
        }
    },
    error: function (e) {
        if (e.status != '200'){
            al.dialog('error','Ocorreu um problema!', e.status + ' ' + e.responseText);
        }
    }
});


$(document).on('blur','.toupper',function (e){
    $(this).val($(this).val().toUpperCase());
});

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

$(document).on('change','[blur="true"]',function(){
    var uri = $(this).attr('blur-uri');
    var cl = $(this).attr('blur-class');
    var el = $(this).parents('form');

    fn.load.blur(uri, $(this), el, cl);
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

$(document).ready(function(){
    fn.load.rotina();
});