function criarTr(...conteudosTd) {
    const tr = document.createElement('tr');
    for (const conteudo of conteudosTd) {
        const td = document.createElement('td');
        td.append(conteudo);
        tr.append(td);
    }
    return tr;
}