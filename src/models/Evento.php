<?php

require_once $root . '/models/CorEvento.php';
require_once $root . '/utils/DateUtil.php';

class Evento
{
    private string $titulo;
    private DateTime $dataInicial;
    private DateTime $dataFinal;
    private string $destino;
    private CorEvento $corEvento;
    private bool $entregue;

    public function titulo(): string
    {
        return $this->titulo;
    }

    public function entregue(): bool 
    {
        return $this->entregue;
    }

    public function dataInicial(): string
    {
        return DateUtil::formatTo($this->dataInicial, 'Y-m-d');
    }

    public function dataFinal(): string
    {
        return DateUtil::formatTo($this->dataFinal, 'Y-m-d');
    }

    public function destino(): string
    {
        return $this->destino;
    }

    public function corEvento(): string
    {
        return $this->corEvento->toString();
    }

    public function setTitulo(string $titulo): Evento
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function setDataInicial(DateTime $dataInicial): Evento
    {
        $this->dataInicial = $dataInicial;
        return $this;
    }

    public function setDataFinal(DateTime $dataFinal): Evento
    {
        $this->dataFinal = $dataFinal;
        return $this;
    }

    public function setDestino(string $destino): Evento
    {
        $this->destino = $destino;
        return $this;
    }

    public function setCorEvento(): Evento 
    {
        date_default_timezone_set("America/Sao_Paulo");
        $diasFaltando = round(((($this->dataFinal->getTimestamp() - time()) / 24) / 60) / 60);
        if ($diasFaltando > 7) {
            $this->corEvento = CorEvento::VERDE;
        } else if ($diasFaltando > 3) {
            $this->corEvento = CorEvento::AMARELO;
        } else {
            $this->corEvento = CorEvento::VERMELHO;
        }

        return $this;
    }

    public function setEntregue(bool $entregue): Evento {
        $this->entregue = $entregue;
        return $this;
    }

    public static function getTempoRestante(DateTime $dataEntrega): string {
        date_default_timezone_set("America/Sao_Paulo");
        $faltandoEntrega = round(((($dataEntrega->getTimestamp() - time()) / 24) / 60) / 60);
        if ($faltandoEntrega > 1) {
            return 'Entrega em ' . intval($faltandoEntrega) . ' dias';
        } else if ($faltandoEntrega == 1) {
            return 'Entrega hoje';
        }

        return 'Atrasado';
    }

    public function toArray(): array
    {
        return [
            'title' => $this->titulo(),
            'start' => $this->dataInicial(),
            'end' => $this->dataFinal(),
            'url' => $this->destino(),
            'color' => $this->corEvento(),
            'className' => 'text-center'
        ];
    }

    public static function tarefasToEventos(array $tarefas): array
    {
        return array_map(
            fn (Tarefa $row) => (new Evento)
                ->setTitulo(self::construirTitulo($row))
                ->setDataInicial($row->dataHoraAbertura())
                ->setDataFinal($row->dataHoraFechamento())
                ->setCorEvento()
                ->setDestino('tarefas/tarefa?id=' . $row->id()),
            $tarefas
        );
    }

    private static function construirTitulo(Tarefa $row) {
        return $row->disciplina()->getNome().
               ' - '.
               $row->titulo().
               ' - '.
               self::getTempoRestante($row->dataHoraEntrega());
    }
}