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

class XMLParser {

    /**
     * creates a DOMDocument and a DOMXPath
     * object for a given html data source
     *
     * @param string $data
     *
     * @throws Exception
     * @access private
     * @return DOMXPath
     */
    private function _getXPath($data) {
        $dom = new DOMDocument($this->_getCleanHTML($data));
        if (!(@$dom->loadHTML($data))) {
            throw new Exception("Could not load DOM from data source");
        }
        return new DOMXPath($dom);
    }

    private function _getCleanHTML($data) {
        if (empty($data)) return '';
        $tidy = new tidy();
        $tidy->ParseString($data);
        $tidy->cleanRepair();
        $output = tidy_get_output($tidy);
        return trim($output);
    }

    /**
     * tries to use xpath queries on a document
     * to get nodevalues
     *
     * @param string $data
     * @param string $query
     *
     * @access public
     * @return string
     */
    public function getNodeValue($data, $query) {
        $domxpath = $this->_getXPath($data);
        $result = $domxpath->query($query);
        $return = '';
        if ($result->length) {
            foreach ($result as $node) {
                $return .= $node->nodeValue;
            }
        }
        return $return;
    }
}
