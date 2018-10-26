<?php declare(strict_types=1);

class GameTest extends PHPUnit\Framework\TestCase
{
  public function testStartGameEvent() {
    $game = new Game();
    $events = $game->getAllevents();
    $this->assertCount(1, $events);
    $this->assertInstanceOf(\phpce\tictactoe\GameStartedEvent::class, $events[0]);
  }

  public function testAddSymbolGameTest() {
    $game = new Game();
    $game->placeSymbol('X', 0);
    $symbols = $game->getSymbolPosition();

    $events = $game->getAllEvents();
    $this->assertCount(2, $events);
    $this->assertInstanceOf(\phpce\tictactoe\GameStartedEvent::class, $events[0]);
    $this->assertInstanceOf(\phpce\tictactoe\SymbolPlacedEvent::class, $events[1]);

    $this->assertEquals('X', $events[1]->symbol());
    $this->assertEquals(0, $events[1]->field());

    $this->assertEquals('X', $symbols[0]);
  }

  /**
   * @expectedException LogicException
   */
  public function testAlreadyOccupiedField() {
    $game = new Game;
    $game->placeSymbol('X', 0);
    $game->placeSymbol('O', 0);
  }

  /**
   * @expectedException OutOfBoundsException
   */
  public function testInvalidField() {
    $game = new Game;
    $game->placeSymbol('X', -1);
  }

  /**
   * @expectedException OutOfBoundsException
   */
  public function testInvalidField2() {
    $game = new Game;
    $game->placeSymbol('X', 9);
  }



  /**
   * @expectedException LogicException
   */
  public function testSameSymbolMoveTwice() {
    $game = new Game();
    $game->placeSymbol('X', 0);
    $game->placeSymbol('X', 1);
  }

  public function testGameWonHorizontally() {
    $game = new Game();
    $game->placeSymbol('X', 0);
    $game->placeSymbol('O', 3);
    $game->placeSymbol('X', 1);
    $game->placeSymbol('O', 5);
    $game->placeSymbol('X', 2);

    $events = $game->getAllEvents();
    $this->assertCount(7, $events);
    $this->assertInstanceOf(\phpce\tictactoe\GameWonEvent::class, $events[5]);
    $this->assertEquals('X', $events[5]->winningSymbol());
  }

  public function testConstructGameWonHorizontallyFromEvent() {
    $events = [
      new \phpce\tictactoe\GameStartedEvent(),
      new \phpce\tictactoe\SymbolPlacedEvent('X', 0),
      new \phpce\tictactoe\SymbolPlacedEvent('Y', 1),
    ];
    $game = new Game($events);
    $symbolPositions = $game->getSymbolPosition();
    $this->assertEquals($symbolPositions[1], 'Y');
  }




}