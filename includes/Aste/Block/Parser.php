<?php

/**
 * Project:     Aste: the PHP template engine 
 * SVN:         $Id: $id$ 
 * 
 * Copyright (C) 2011 Andrey Tykhonov 
 * 
 * This program is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version. 
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 * 
 * You should have received a copy of the GNU General Public License 
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 * 
 * @copyright 2011 Andrey Tykhonov 
 * @author Andrey Tykhonov <atykhonov@gmail.com> 
 * @package Aste 
 * @version 1.0 beta 
 */

/**
 * Parser does template parsing
 */
class Aste_Block_Parser {

    // template delimiters and marks
    const LEFT_DELIMITER = '{';
    const END_BLOCK_MARK = '/';
    const RIGHT_DELIMITER = '}';
    const VAR_MARK = '$';
    const FAKE_TAG_PREFIX = '{@%@';
    const FAKE_RIGHT_DELIMITER = '@%@}';

    // template content
    private $content = null;

    // template pattern
    private $pattern = null;
        
    /**
     * Class constructor
     * 
     * @param string $content 
     * @access public
     * @return void
     */
    public function __construct($content = '') {
        
        $this->setContent($content);
        $this->setPattern($content);
    }

    /**
     * Set template content
     * 
     * @param string $content 
     * @access public
     * @return void
     */
    public function setContent($content) {

        $this->content = $content;
    }

    /**
     * Returns template content
     * 
     * @access public
     * @return string
     */
    public function getContent() {

        return $this->content;
    }

    /**
     * Set pattern
     * 
     * @param string $content 
     * @access private
     * @return void
     */
    private function setPattern($content) {

        $this->pattern = $content;
    }

    /**
     * Returns pattern
     * 
     * @access private
     * @return string
     */
    public function getPattern() {

        return $this->pattern;
    }

    /**
     * Fetchs content of the child block by block name
     * 
     * @param string $name 
     * @access public
     * @return string
     */
    public function fetchBlockContent($name) {

        $pattern = $this->getFetchBlockPattern($name);

        $matches = array();
        if (preg_match($pattern, $this->getContent(), $matches)) {
            
            $this->replaceBlockWithPseudo($name);
            return $matches[2];

        } else {
            
            throw new Aste_Exception(sprintf('Block "%s" does not exist!', $name));
        }
    }

    private function replaceBlockWithPseudo($pseudo) {

        $replacement = self::FAKE_TAG_PREFIX . $pseudo . self::FAKE_RIGHT_DELIMITER;

        $blockContent = preg_replace($this->getFetchBlockPattern($pseudo), $replacement, $this->getContent()); 

        $this->setContent(trim($blockContent));
    }

    private function getFetchBlockPattern($name) {

        return '#(' . self::LEFT_DELIMITER 
                        . $name 
                        . self::RIGHT_DELIMITER 
                        . ')(.*)(' 
                        . self::LEFT_DELIMITER
                        . self::END_BLOCK_MARK 
                        . $name 
                        . self::RIGHT_DELIMITER 
                        . ')#is';
    }

    /**
     * Fetchs block content
     * 
     * @param Aste_Block $block 
     * @access public
     * @return void
     */
    public function fetch($block) {

        $fake_tag = self::FAKE_TAG_PREFIX . $block->getName() . self::FAKE_RIGHT_DELIMITER;
        $fake_tag_length = strlen($fake_tag);
        $fake_tag_begin = strpos($this->getContent(), $fake_tag);

        if ($fake_tag_begin !== false) {

            if ($block->isDisplayable() === true) {

                $content = substr_replace($this->getContent(), $block->fetch(), $fake_tag_begin, $fake_tag_length);
            } elseif($block->isDisplayable() === false) {

                $content = substr_replace($this->getContent(), '', $fake_tag_begin, $fake_tag_length);
            } else {

                $content = substr_replace($this->getContent()
                                            , self::LEFT_DELIMITER 
                                                . $block->getName()
                                                . self::RIGHT_DELIMITER 
                                                . $block->getContent() 
                                                . self::LEFT_DELIMITER
                                                . self::END_BLOCK_MARK 
                                                . $block->getName()
                                                . self::RIGHT_DELIMITER
                                            , $fake_tag_begin, $fake_tag_length);
            }

            $this->setContent($content);
        }

        // replaces variable which are used for recursive loops
        $this->parseVar($block->getName(), '');
    }

    /**
     * Parses template variable
     * 
     * @param string $name - variable name
     * @param string $value - variable value
     * @access public
     * @return void
     */
    public function parseVar($name, $value) {

        $varTag = self::LEFT_DELIMITER . self::VAR_MARK . $name . self::RIGHT_DELIMITER;
        $varTagLen = strlen($varTag);
        $varTagPos = 0;
        $content = $this->getContent();
        do {
    	    $varTagPos = strpos($content, $varTag, $varTagPos);
    	    if ($varTagPos !== false) {

        	$content = substr_replace($content, $value, $varTagPos, $varTagLen);
            
    	    }
        } while($varTagPos !== false);

    	$this->setContent($content);
    }

    /**
     * Prepares the template for the recursive loop
     * 
     * @param string $name - block name
     * @access public
     * @return void
     */
    public function rloop($name) {

        $this->parseVar($name, self::FAKE_TAG_PREFIX . $name . self::FAKE_RIGHT_DELIMITER);
    }
}
