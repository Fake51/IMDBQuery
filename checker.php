<?php
    /**
     * Copyright 2010 Peter Lind. All rights reserved.

     * Redistribution and use in source and binary forms, with or without modification, are
     * permitted provided that the following conditions are met:

     *    1. Redistributions of source code must retain the above copyright notice, this list of
     *       conditions and the following disclaimer.

     *    2. Redistributions in binary form must reproduce the above copyright notice, this list
     *       of conditions and the following disclaimer in the documentation and/or other materials
     *       provided with the distribution.

     * THIS SOFTWARE IS PROVIDED BY Peter Lind ``AS IS'' AND ANY EXPRESS OR IMPLIED
     * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
     * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Peter Lind OR
     * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
     * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
     * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
     * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
     * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
     * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

     * The views and conclusions contained in the software and documentation are those of the
     * authors and should not be interpreted as representing official policies, either expressed
     * or implied, of Peter Lind.
     *
     * PHP version 5
     *
     * @package   IMDBQuery
     * @author    Peter Lind <peter@plphp.dk>
     * @copyright 2010 Peter Lind
     * @license   http://plind.dk/imdbquery/#license New BSD License
     * @link      http://www.github.com/Fake51/IMDBQuery
     */

require __DIR__ . '/curler.php';
require __DIR__ . '/xmlparser.php';
require __DIR__ . '/page.php';
require __DIR__ . '/movie.php';
require __DIR__ . '/search.php';
require __DIR__ . '/profile.php';

class Checker {

    const IMDB_SEARCH_PAGE = "/find?s=all;q=";
    const IMDB_URL_BASE = "http://www.imdb.com";

    private $_name;

    private $_curler;

    private $_xml_parser;

    private $_ratings = array();

    private $_movies = array();

    private $_errors = array();

    public function __construct($name) {
        $this->_name = $name;
        $this->_curler = new Curler;
        $this->_xml_parser = new XMLParser;
    }

    /**
     * wrapper for IMDB check of author
     *
     * @access public
     * @return float
     */
    public function getRating() {
        $this->getProfilePage();
        $this->getMovies();
        foreach ($this->_movies as $key => $movie) {
            $this->_ratings[$key] = $movie->getRating();
        }
        $this->_curler->cleanup();
        return $this->getRatingAverage();
    }

    /**
     * searches for an actor page
     * and parses the result, then
     * gets the actor page, and fetches the
     * individual movie pages
     *
     * @access private
     * @return void
     */
    private function getMovies() {
        foreach ($this->_profile_page->getMovieLinks() as $movie_link) {
            try {
                $this->_movies[] = new Movie($this->_curler->fetchPage(self::IMDB_URL_BASE . $movie_link['url']), $this->_xml_parser);
            } catch(Exception $e) {
                $this->_errors[] = "Skipping link: {$movie_link['url']}. Reason: {$e->getMessage()}." . PHP_EOL;
            }
        }
    }

    /**
     * search for the actors profile page
     * and fetch it if possible
     *
     * @access private
     * @return void
     */
    private function getProfilePage() {
        $temp_page = $this->_curler->fetchPage(self::IMDB_URL_BASE . self::IMDB_SEARCH_PAGE . urlencode($this->_name));
        if (!stripos($temp_page, '<title>' . $this->_name)) {
            $search = new Search($temp_page, $this->_xml_parser);
            $temp_page = $this->_curler->fetchPage(self::IMDB_URL_BASE . $search->getActorLink());
        }
        $this->_profile_page = new Profile($temp_page, $this->_xml_parser);
    }

    /**
     * returns the average of all the rated movies
     *
     * @access public
     * @return float
     */
    public function getRatingAverage() {
        $i = 0;
        $sum = 0;
        foreach ($this->_ratings as $rating) {
            if (!$rating) continue;
            $sum += $rating;
            $i++;
        }
        return $sum / floatval($i);
    }

    /**
     * returns the best movie found
     *
     * @throws Exception
     * @access public
     * @return Movie
     */
    public function getBestMovie() {
        if (empty($this->_movies)) {
            throw new Exception("No movies found");
        }
        $best = null;
        foreach ($this->_movies as $movie) {
            if (!$movie->getRating()) continue;
            if (empty($best) || $movie->getRating() > $best->getRating()) {
                $best = $movie;
            }
        }
        return $best;
    }

    /**
     * returns the worst movie found
     *
     * @access public
     * @return Movie
     */
    public function getWorstMovie() {
        if (empty($this->_movies)) {
            throw new Exception("No movies found");
        }
        $worst = null;
        foreach ($this->_movies as $movie) {
            if (!$movie->getRating()) continue;
            if (empty($worst) || $movie->getRating() < $worst->getRating()) {
                $worst = $movie;
            }
        }
        return $worst;
    }

    /**
     * returns the number of movies found for the
     * actor/actress
     *
     * @access public
     * @return int
     */
    public function getMovieCount() {
        return count($this->_movies);
    }

    /**
     * returns the errors encountered during the check
     *
     * @access public
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }
}
