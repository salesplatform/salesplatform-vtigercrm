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

class Aste_Template {

    const DEFAULT_BLOCK_NAME = 'main';

    // template file
    private $template = null;

    // template preview
    private $preview = null;

    // directory with templates
    private $directory = null;

    // main block
    private $mainBlock = null;
    
    /**
     * Class constructor
     * 
     * @param string $template - path to template
     * @param Aste_Template_Preview $preview 
     * @access public
     * @return void
     */
    public function __construct($template = null, $preview = null) {

        if ($template != null) {
            $this->setTemplate($template);
        }
        $this->setPreview($preview);
    }

    /**
     * Set path to a file of template
     * 
     * @param string $template 
     * @access public
     * @return void
     */
    public function setTemplate($template) {

//        if ($this->getTemplateDirectory() != null) {
//    
//            $template = $this->getTemplateDirectory() . DIRECTORY_SEPARATOR . $template;
//        }
//
//        if (!file_exists($template)) {
//        
//            throw new Aste_Exception(sprintf('File "%s" does not exist', $template));
//        }
        
        $this->template = $template;
    }

    /**
     * Set template directory 
     * 
     * @param string $directory - directory path
     * @access public
     * @return void
     */
    public function setTemplateDirectory($directory) {

        $this->directory = $directory;
    }

    /**
     * Returns path to directory
     * 
     * @access protected
     * @return string
     */
    protected function getTemplateDirectory() {

        return $this->directory;
    }

    /**
     * Set template preview
     * 
     * @param Aste_Template_Preview $preview 
     * @access public
     * @return void
     */
    public function setPreview($preview) {

        $this->preview = $preview;
    }

    /**
     * Returns block by name
     * 
     * @param string $name block name 
     * @param bool $cyclic indicates whether block is cyclic
     * @access public
     * @return Aste_Block
     */
    public function getBlock($name, $cyclic = false) {

        $this->initMainBlock();

        return $this->mainBlock->getBlock($name, $cyclic);
    }

    private function initMainBlock() {
        
        if ($this->mainBlock == null) {
            
//            $this->mainBlock = new Aste_Block(self::DEFAULT_BLOCK_NAME, file_get_contents($this->template)); 
            $this->mainBlock = new Aste_Block(self::DEFAULT_BLOCK_NAME, $this->template); 
            $this->mainBlock->display();
        }
    }

    /**
     * Makes a loop for the cyclic block iterations
     * 
     * @param string $name 
     * @access public
     * @return Aste_Block
     */
    public function loop($name) {

        $this->initMainBlock();

        return $this->mainBlock->loop($name);
    }

    /**
     * Fetchs parsed template content
     * 
     * @access public
     * @return string
     */
    public function fetch() {

        return $this->mainBlock->fetch();
    }

}
