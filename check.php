#!/usr/bin/php
<?php

require __DIR__ . '/checker.php';

if (empty($_SERVER['argv'][1])) {
    die("Call script with name of actor to check" . PHP_EOL);
}

$name = $_SERVER['argv'][1];

$checker = new Checker($name);
try {
    echo "Actors IMDB rating (average rating of movies played in):" . PHP_EOL;
    echo "- " . $checker->getRating() . " based on " . $checker->getMovieCount() . " movies." . PHP_EOL;
    echo "- Best movie: " . $checker->getBestMovie()->getTitle() . ": " . $checker->getBestMovie()->getRating() . PHP_EOL;
    echo "- Worst movie: " . $checker->getWorstMovie()->getTitle() . ": " . $checker->getWorstMovie()->getRating() . PHP_EOL;
    echo PHP_EOL;
    if ($checker->getErrors()) {
        echo "Errors:" . PHP_EOL;
        foreach ($checker->getErrors() as $error) {
            echo "- " . $error . PHP_EOL;
        }
    }
} catch(Exception $e) {
    die($e->getMessage() . PHP_EOL);
}
