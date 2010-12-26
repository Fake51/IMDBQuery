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

class Profile extends Page {

    private $_movie_links;

    private $_misc_blocks = array(
        'Producer',
        'Writer',
        'Thanks',
        'Self',
        'ArchiveFootage',
        'Soundtrack',
    );

    /**
     * parses an html page from imdb to get
     * the link for all movies mentioned on it
     *
     * @throws Exception
     * @access protected
     * @return void
     */
    protected function _parsePage() {
        // hack. DOMDoc cant properly parse the
        // html, so resorting to regexes
        if (strpos($this->_data, 'id="filmography"') === false) {
            throw new Exception("No filmography on page");
        }

        $temp = substr($this->_data, strpos($this->_data, 'id="filmo-head-Act'));
        foreach ($this->_misc_blocks as $block) {
            if (strpos($temp, 'filmo-head-' . $block)) {
                $temp = substr($temp, 0, strpos($temp, 'filmo-head-' . $block));
            }
        }

        if (strpos($temp, 'div class="article"')) {
            $temp = substr($temp, 0, strpos($temp, 'div class="article"'));
        }

        $temp = preg_replace('#<div[^>]+filmo-episodes.+?</div>#ms', '', $temp);
        preg_match_all('#<a[^>]+href=([^> ]+)>([^<]+)</a>#', $temp, $matches);
        $results = array();
        foreach ($matches[1] as $key => $url) {
            if (strpos($url, 'title')) {
                $results[] = array('title' => $matches[2][$key], 'url' => trim($url, "\"' \t\n\r\x00\x0b"));
            }
        }

        $this->_movie_links = $results;
    }

    public function getMovieLinks() {
        return $this->_movie_links;
    }
}
