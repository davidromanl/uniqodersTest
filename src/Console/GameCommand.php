<?php

namespace Uniqoders\Game\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class GameCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('game')
            ->setDescription('New game: you vs computer')
            ->addArgument('name', InputArgument::OPTIONAL, 'what is your name?', 'Player 1');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write(PHP_EOL . 'Made with ♥ by Uniqoders. Modified by DavidRomanL' . PHP_EOL . PHP_EOL);

        $player_name = $input->getArgument('name');

        $players = [
            'player' => [
                'name' => $player_name,
                'stats' => [
                    'draw' => 0,
                    'victory' => 0,
                    'defeat' => 0,
                ]
            ],
            'computer' => [
                'name' => 'Computer',
                'stats' => [
                    'draw' => 0,
                    'victory' => 0,
                    'defeat' => 0,
                ]
            ]
        ];

        // Weapons available
        $weapons = [
            0 => 'Tijera',
            1 => 'Piedra',
            2 => 'Papel',
            3 => 'Lagarto',
            4 => 'Spock'
        ];

        // Rules to win
        $rules = [
            0 => [2,3],
            1 => [0,3],
            2 => [1,4],
            3 => [2,4],
            4 => [0,1],
        ];

        $round = 1;
        $max_round = 5;

        $ask = $this->getHelper('question');

        do {
            // User selection
            $question = new ChoiceQuestion('Selecciona el arma: ', array_values($weapons), 1);
            $question->setErrorMessage('Arma %s invalida.');

            
            $user_weapon = $ask->ask($input, $output, $question);
            $output->writeln($player_name . ' ha seleccionado: ' . $user_weapon);
            $user_weapon = array_search($user_weapon, $weapons);

            // Computer selection
            $computer_weapon = array_rand($weapons);
            $output->writeln('Computer ha seleccionado: ' . $weapons[$computer_weapon]);

            if ($rules[$user_weapon][0] === $computer_weapon || $rules[$user_weapon][1] === $computer_weapon) {
                $players['player']['stats']['victory']++;
                $players['computer']['stats']['defeat']++;

                $output->writeln('Gana ' . $player_name . '!');
            } else if ($rules[$computer_weapon][0] === $user_weapon || $rules[$computer_weapon][1] === $user_weapon) {
                $players['player']['stats']['defeat']++;
                $players['computer']['stats']['victory']++;

                $output->writeln('Gana Computer!');
            } else {
                $players['player']['stats']['draw']++;
                $players['computer']['stats']['draw']++;

                $output->writeln('Empate!');
            }

            $round++;
        } while ($round <= $max_round);

        // Display stats
        $stats = $players;

        $stats = array_map(function ($player) {
            return [$player['name'], $player['stats']['victory'], $player['stats']['draw'], $player['stats']['defeat']];
        }, $stats);

        $table = new Table($output);
        $table
            ->setHeaders(['Jugador', 'Ganadas', 'Empatadas', 'Perdidas'])
            ->setRows($stats);

        $table->render();

        return 0;
    }
}
