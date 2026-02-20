<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * LetsCatchTheLion implementation : © <Mathieu Chatrain> <mathieu.chatrain@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * LetsCatchTheLion game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

$this->types = [
  1=>clienttranslate("the chick"),
  2=>clienttranslate("the elephant"),
  3=>clienttranslate("the giraffe"),
  4=>clienttranslate("the lion"),
  5=>clienttranslate("the hen"),
];

$this->coords = [
  'square_1_1'=>clienttranslate("A1"),
  'square_1_2'=>clienttranslate("A2"),
  'square_1_3'=>clienttranslate("A3"),
  'square_1_4'=>clienttranslate("A4"),
  'square_2_1'=>clienttranslate("B1"),
  'square_2_2'=>clienttranslate("B2"),
  'square_2_3'=>clienttranslate("B3"),
  'square_2_4'=>clienttranslate("B4"),
  'square_3_1'=>clienttranslate("C1"),
  'square_3_2'=>clienttranslate("C2"),
  'square_3_3'=>clienttranslate("C3"),
  'square_3_4'=>clienttranslate("C4"),
  
];

