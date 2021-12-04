function criarTr(conteudos, tag = 'td') {
    const tr = document.createElement('tr');
    for (const conteudo of conteudos) {
        const td = document.createElement(tag);
        td.append(conteudo);
        tr.append(td);
    }
    return tr;
}