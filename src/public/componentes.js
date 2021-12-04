function criarTr(conteudos, tag = 'td') {
    const tr = document.createElement('tr');
    for (const conteudo of conteudos) {
        const td = document.createElement(tag);
        td.append(conteudo);
        tr.append(td);
    }
    return tr;
}

function criarElemento(tag, classes=[], pai=null, atributos={}) {
    const elem = document.createElement(tag);
    pai?.append(elem);
    elem.classList.add(...classes);
    Object.assign(elem, atributos);
    return elem;
}