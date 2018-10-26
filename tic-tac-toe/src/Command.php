<?php declare(strict_types=1);

namespace phpce\tictactoe;

interface Command
{
  public function handler() :string;
}
