<?php
class Game
{
  private $symbolPositions = [];

  private $events = [];

  public function __construct(array $events = []) {
    if (!empty($events)) {
      foreach ($this->events as $event) {
        $this->emit($event);
      }
    } else {
      $gameStartEvent = new \phpce\tictactoe\GameStartedEvent();
      $this->emit($gameStartEvent);
    }
  }

  public function getAllEvents() :array {
    return $this->events;
  }

  public function getSymbolPosition() :array {
    return $this->symbolPositions;
  }

  public function placeSymbol(string $symbol, int $postion) {
    $symbolPlacedEvent = new \phpce\tictactoe\SymbolPlacedEvent($symbol, $postion);
    $this->emit($symbolPlacedEvent);
  }

  private function emit(\phpce\tictactoe\Event $event) {
    $this->applyEvent($event);
    $this->events[] = $event;
  }


  private function applyEvent(\phpce\tictactoe\Event $event){
    switch (\get_class($event)){
      case \phpce\tictactoe\GameStartedEvent::class;
        $this->applyGameStartEvent($event);
        break;
      case \phpce\tictactoe\SymbolPlacedEvent::class;
        $this->applyPlaceSymbolEvent($event);
        break;
    }
  }

  private function applyGameStartEvent(\phpce\tictactoe\GameStartedEvent $event) {
    $this->symbolPositions = array_fill(0,8, '');
  }

  private function applyPlaceSymbolEvent(\phpce\tictactoe\SymbolPlacedEvent $event) {
    $this->checkOccupiedField($event);
    $this->checkValidPlaceSymbolEvent($event);
    $this->checkInRangeField($event);

    $this->symbolPositions[$event->field()] = $event->symbol();

    $winningSymbol = $this->getGameWonHorizontallySymbol();
    if (!empty($winningSymbol)) {
      $gameWonEVent = new \phpce\tictactoe\GameWonEvent($winningSymbol);
      $this->emit($gameWonEVent);
    }
  }
  private function checkInRangeField(\phpce\tictactoe\SymbolPlacedEvent $event) {
    if ($event->field() >= 8 || $event->field() < 0) {
      throw new OutOfBoundsException(sprintf('field %s is out of range', $event->field()));
    }
  }


  private function checkOccupiedField(\phpce\tictactoe\SymbolPlacedEvent $event) {
    if (!empty($this->symbolPositions[$event->field()])) {
      throw new LogicException(sprintf('field %s already occupied', $event->field()));
    }
  }

  private function checkValidPlaceSymbolEvent(\phpce\tictactoe\SymbolPlacedEvent $event) {
    $latestEvent = end($this->events);
    if ($latestEvent instanceof \phpce\tictactoe\SymbolPlacedEvent) {
      if ($latestEvent->symbol() == $event->symbol()) {
        throw new LogicException('invalid move , sampe symbol can not be places two times in a row');
      }
    }
  }

  private function getGameWonHorizontallySymbol() :string {
    for ($y = 0; $y<=2;++$y)
    {
      $currentSymbol = '';
      $currentSymbolCount = 0;
      for ($x = 0; $x<=2;++$x)
      {
        $currentPosition = $x+$y;
        if (empty($this->symbolPositions[$currentPosition])){
          continue;
        }

        if (empty($currentSymbol)) {
          $currentSymbol = $this->symbolPositions[$currentPosition];
        }

        if ($currentSymbol == $this->symbolPositions[$currentPosition]) {
          ++$currentSymbolCount;
        }

        if ($currentSymbolCount == 3) {
          return $currentSymbol;
        }
      }
    }

    return '';
  }
}