var typefield = function(element){
    var field = "";
    var item = [];
    var value = ('valor' in element) ? element.valor : element.valorPadrao;

    value = (value == null ? '' : value);


    switch (element.tipo){
        case 'checkbox':
            var v = element.item.split(',');
            if (v[0].indexOf("|") > -1){
                var lb1 = v[0].split('|');
                var lb2 = v[1].split('|');

                field = `<div class="form-check mt-1 ms-2">
                    <input class="form-check-input ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" type="checkbox" name="${element.coluna}" value="${value}" data-checked-on="${lb1[0]}" data-checked-off="${lb2[0]}" ${value == lb1[0] ? 'checked' : ''}>
                        <label class="form-check-label">${lb1[1]}</label>
                    </div>`;
            } else {
                field = `<div class="form-check mt-1 ms-2">
                    <input class="form-check-input ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" type="checkbox" name="${element.coluna}" value="${value}" data-checked-on="${v[0]}" data-checked-off="${v[0]}" ${value == v[0] ? 'checked' : ''}>
                    <label class="form-check-label">${v[0]}</label>
                    </div>`;
            }
            break;
        case 'textarea':
            field = `<textarea  class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" name="${element.coluna}" ${element.flagBloqueado == 'S' ? ' disabled' : ''} ${element.flagObrigatorio == 'S' ? 'required' : ''}>${value}</textarea>`;
            break;
        case 'select':
            var options = "";
            if (element.item != ""){
                var itens = element.item.split(',');
                $.each(itens, function(i,v){
                    var e = v.split('|');
                    options += `<option value="${e[0]}" ${value == e[0] ? 'selected' : ''}>${e[1]}</option>`;
                });
            }
            field = `<select name="${element.coluna}" class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" ${element.flagObrigatorio == 'S' ? 'required' : ''}>${options}</select>`;
            break;
        case 'textRange':
            field = `<input type="text" class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" name="${element.coluna}[]" value="${value}" autocomplete="off" style="width:50%; display:inline-block;" ${element.flagBloqueado == 'S' ? ' disabled' : ''} ${element.flagObrigatorio == 'S' ? 'required' : ''}><input type="text" class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" name="${element.coluna}[]" value="${value}" autocomplete="off" style="width:50%; display:inline-block;" ${element.flagBloqueado == 'S' ? ' disabled' : ''} ${element.flagObrigatorio == 'S' ? 'required' : ''}>`;
            break;
        default:
            if (element.flagBotaoForm == 'S'){
                field = `<div class="input-group"><input type="text" class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" name="${element.coluna}" value="${value}" autocomplete="off" ${element.flagObrigatorio == 'S' ? 'required' : ''}><button class="btn btn-outline-secondary" type="button" onclick="${element.btnFuncao}"><i class="fas fa-ellipsis-h"></i></button></div>`;
            } else if (element.flagConsultaForm == 'S'){
                field = `<div class="input-group"><input type="text" class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" name="${element.coluna}" value="${value}" style="max-width: 120px" autocomplete="off" ${element.flagBloqueado == 'S' ? ' disabled' : ''} ${element.flagObrigatorio == 'S' ? 'required' : ''} blur="true" blur-uri="${element.uriConsulta}" blur-class="${element.classIndex}"><button class="btn btn-outline-secondary" type="button" onclick="${element.btnFuncao}" ${element.flagBloqueado == 'S' ? ' disabled' : ''}><i class="fas fa-ellipsis-h"></i></button><input type="text" class="form-control ${element.classIndex}" name="label" disabled></div>`;
            } else {
                field = `<input type="text" class="form-control ${element.flagEditavel == 'S' ? 'f-input' : ''} ${element.classIndex}" name="${element.coluna}" value="${value}" autocomplete="off" ${element.flagBloqueado == 'S' ? ' disabled' : ''} ${element.flagObrigatorio == 'S' ? 'required' : ''}>`;
            }
            break;
    }

    return field;
};

$(document).on('change','input[type="checkbox"]',function(){
    var el = $(this)[0];
    if (el.checked){
        $(el).val($(el).attr('data-checked-on'));
    } else {
        $(el).val($(el).attr('data-checked-off'));
    }
});

