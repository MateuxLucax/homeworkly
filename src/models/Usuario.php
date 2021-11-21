<?php

class Usuario
{
    private int $id_usuario;
    private string $tipo;
    private string $nome;
    private string $login;
    private string $hash_senha;
    private DateTime $cadastro;
    private DateTime $ultimo_acesso;

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(int $id_usuario): Usuario
    {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): Usuario
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): Usuario
    {
        $this->nome = $nome;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): Usuario
    {
        $this->login = $login;
        return $this;
    }

    public function getHashSenha(): string
    {
        return $this->hash_senha;
    }

    public function setHashSenha(string $hash_senha): Usuario
    {
        $this->hash_senha = $hash_senha;
        return $this;
    }

    public function getCadastro(): DateTime
    {
        return $this->cadastro;
    }

    public function setCadastro(DateTime $cadastro): Usuario
    {
        $this->cadastro = $cadastro;
        return $this;
    }

    public function getUltimoAcesso(): DateTime
    {
        return $this->ultimo_acesso;
    }

    public function setUltimoAcesso(DateTime $ultimo_acesso): Usuario
    {
        $this->ultimo_acesso = $ultimo_acesso;
        return $this;
    }
}