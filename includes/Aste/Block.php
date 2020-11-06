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
 * @version 1.1 beta 
 */

/**
 * Block is piece of template which represents in template as text wrapped by such tags as {block} and {/block}
 */
class Aste_Block {

    // block name
    private $name = null;

    // parser used by block
    private $parser = null;

    // child blocks
    private $blocks = array();

    // blocks which are used in cycles
    private $cyclicBlocks = array();

    // indicates whether display block
    private $display = false;

    /**
     * Class constructor 
     * 
     * @param string $name block name 
     * @param string $content block content 
     * @access public
     */
    public function __construct($name, $content) {

        $this->setName($name);
        $this->initParser($content);
    }

    /**
     * Set block name
     * 
     * @param string $name 
     * @access protected
     * @return void
     */
    protected function setName($name) {

        $this->name = $name;
    }

    /**
     * Returns block name
     * 
     * @access public
     * @return string
     */
    public function getName() {

        return $this->name;
    }

    /**
     * Initiates block parser
     * 
     * @param string $content 
     * @access protected
     * @return void
     */
    protected function initParser($content) {
        
        $this->parser = new Aste_Block_Parser($content); 
    }

    /**
     * Adds block
     * 
     * @param Aste_Block $block 
     * @access private
     * @return void
     */
    private function addBlock($block) {

        $this->blocks[$block->getName()] = $block;
    }

    /**
     * Adds blocks 
     * 
     * @param array of Aste_Block $blocks 
     * @access private
     * @return void
     */
    private function addBlocks($blocks) {

        foreach($blocks as $block) {

            $this->addBlock($block);
        }
    }

    /**
     * Returns children blocks
     * 
     * @access public
     * @return array
     */
    public function getBlocks() {

        return $this->blocks;
    }

    /**
     * Returns child block by name
     * 
     * @param string $name 
     * @access private
     * @return Aste_Block
     */
    private function getChildBlock($name) {

        if (!empty($this->blocks[$name])) {
            
            return $this->blocks[$name];
        }

        return null;
    }

    /**
     * Returns child block by name 
     * 
     * @param string $name - block name
     * @param bool $cyclic - indicates whether block is cyclic
     * @access public
     * @return Aste_Block
     */
    public function getBlock($name, $cyclic = false) {

        $block = $this->getChildBlock($name);
        if ($block == null) {

            $content = $this->parser->fetchBlockContent($name);

            $block = new Aste_Block($name, $content);
            
            $this->addBlock($block);
        }

        if ($cyclic) {

            return $this->loop($name);
        } else {

            return $block;
        }
    }

    /**
     * Fetchs block content. Returns parsed template content.
     * 
     * @access public
     * @return string
     */
    public function fetch() {

        foreach($this->getBlocks() as $block) {

            if (array_key_exists($block->getName(), $this->cyclicBlocks)) {
                
                $blockContent = '';
                foreach($this->cyclicBlocks[$block->getName()] as $block) {
                    
                    $blockContent .= $block->fetch();
                }

                $block->resetContent($blockContent);
            }            
            
            $this->parser->fetch($block);

            // replaces variable which are used for recursive loops
            $this->parser->parseVar($block->getName(), '');
        }
        
        return $this->parser->getContent();
    }

    /**
     * Makes clone of the instances
     * 
     * @access public
     * @return void
     */
    public function __clone() {

        // reinitiates parser
        $this->parser = new Aste_Block_Parser($this->parser->getContent());
    }

    /**
     * Set block display status
     * 
     * @param bool $display 
     * @access public
     * @return void
     */
    public function display($display = true) {
        
        $this->display = $display;
    }

    /**
     * Is block displayable
     * 
     * @access public
     * @return bool
     */
    public function isDisplayable() {

        return $this->display;
    }

    /**
     * Set block variable
     * 
     * @param string $name variable name
     * @param string $value variable value
     * @access public
     * @return void
     */
    public function setVar($name, $value) {

        $this->parser->parseVar($name, $value);
    }

    /**
     * Does recursive loop for block
     * 
     * @access public
     * @return void
     */
    public function rloop() {

        $this->parser->rloop($this->getName());

        $block = new Aste_Block($this->getName(), $this->parser->getPattern());
        $this->addBlock($block);
    }

    /**
     * Resets content for the block
     * 
     * @param string $content 
     * @access public
     * @return void
     */
    public function resetContent($content) {

        $this->parser->setContent($content);
    }

    /**
     * Makes cyclic block
     * 
     * @param string $name - block name
     * @access public
     * @return Aste_Block
     */
    public function loop($name) {

        $block = $this->getBlock($name);

        $clonedBlock = clone $block;
        $clonedBlock->display();
        $this->cyclicBlocks[$name][] = $clonedBlock;

        return $clonedBlock;
    }

}
