<?php
class CommandHandler
{
  /**
   * @var \phpce\tictactoe\EventStore
   */
  private $eventStore;

  public function handle(\phpce\tictactoe\Command $command) {
    switch (\get_class($command)) {
      case \phpce\tictactoe\StartGameCommand::class:
        $this->handleStartCommand($command);
        break;
      case \phpce\tictactoe\PlaceSymbolCommand::class:
        $this->handlePlaceSymbolCommand($command);
        break;

    }
  }

  private function getGame() :Game {
    $events = $this->eventStore->loadAll();
    return new Game($events);
  }

  private function handleStartCommand(\phpce\tictactoe\StartGameCommand $command) {
    $this->getGame();
  }

  private function handlePlaceSymbolCommand(\phpce\tictactoe\PlaceSymbolCommand $command) {
    $game = $this->getGame();
    $game->placeSymbol($command->symbol(), $command->field());
  }

}