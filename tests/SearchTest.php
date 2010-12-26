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
     * @package   Tests
     * @author    Peter Lind <peter@plphp.dk>
     * @copyright 2010 Peter Lind
     * @license   http://plind.dk/imdbquery/#license New BSD License
     * @link      http://www.github.com/Fake51/IMDBQuery
     */

require __DIR__ . '/../page.php';
require __DIR__ . '/../search.php';

class SearchTest extends PHPUnit_Framework_TestCase {

    public function testBadConstruct() {
        $parser = $this->getMock('XMLParser', array('getNodeValue'));
        $this->setExpectedException("Exception");
        $movie = new Search('', $parser);
    }

    public function testMovie() {
        $parser = $this->getMock('XMLParser', array('getNodeValue'));
        $parser->expects($this->once())
            ->method('getNodeValue')
            ->with($this->anything(), $this->anything())
            ->will($this->returnValue('/name/nm0000612/'));
        $search = new Search(file_get_contents('/tmp/test'), $parser);
        $this->assertTrue($search->getActorLink() == '/name/nm0000612/');
    }

    public function testBadRating() {
        $parser = $this->getMock('XMLParser', array('getNodeValue'));
        $parser->expects($this->any())
            ->method('getNodeValue')
            ->with($this->anything(), $this->anything())
            ->will($this->returnValue(''));
        $this->setExpectedException("Exception");
        $search = new Search(file_get_contents('/tmp/test'), $parser);
    }
}
