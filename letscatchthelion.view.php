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
 * letscatchthelion.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in letscatchthelion_letscatchthelion.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
require_once( APP_BASE_PATH."view/common/game.view.php" );
  
class view_letscatchthelion_letscatchthelion extends game_view
{
    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "letscatchthelion";
    }
    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );

        /*********** Place your code below:  ************/

        
        $current_player_id = $this->getCurrentPlayerId();
        $spectator = $this->game->isSpectator();
        
        if($spectator === false)
        {
            $current_color = $players[$current_player_id]['player_color'];
        }

        if($spectator === true)
        {
            $current_color = '008c00';
        }
        
        
              
        if ($current_color == '008c00')
        {
        $this->page->begin_block( "letscatchthelion_letscatchthelion", "square" );
        
        $hor_scale = 100;
        $ver_scale = 100;
        for( $x=1; $x<=3; $x++ )
        {
            for( $y=1; $y<=4; $y++ )
            {
                $this->page->insert_block( "square", array(
                    'X' => $x,
                    'Y' => $y,
                    'LEFT' => round( ($x-1)*$hor_scale ),
                    'TOP' => round( ($y-1)*$ver_scale )
                ) );
            }        
        }
        }

        if ($current_color == '0000ff')
        {
        $this->page->begin_block( "letscatchthelion_letscatchthelion", "square" );
        
        $hor_scale = 100;
        $ver_scale = 100;
        for( $x=1; $x<=3; $x++ )
        {
            for( $y=1; $y<=4; $y++ )
            {
                $this->page->insert_block( "square", array(
                    'X' => 4-$x,
                    'Y' => 5-$y,
                    'LEFT' => round( ($x-1)*$hor_scale ),
                    'TOP' => round( ($y-1)*$ver_scale )
                ) );
            }        
        }
        }

        if ($current_color == '008c00')
        {
        $this->page->begin_block( "letscatchthelion_letscatchthelion", "squarereserve1" );
        
        $hor_scale = 100;
        $ver_scale = 100;
        for( $x=1; $x<=3; $x++ )
        {
            $this->page->insert_block( "squarereserve1", array(
                    'X' => $x,
                    'LEFT' => 0,
                    'TOP' => round( ($x-1)*$ver_scale ),
                    
            ) );
                 
        }
        for( $x=4; $x<=6; $x++ )
        {
            $this->page->insert_block( "squarereserve1", array(
                    'X' => $x,
                    'LEFT' => 100,
                    'TOP' => round( ($x-4)*$ver_scale ),
                    
            ) );
                 
        }

        



        $this->page->begin_block( "letscatchthelion_letscatchthelion", "squarereserve2" );
        
        $hor_scale = 100;
        $ver_scale = 100;
        for( $x=1; $x<=3; $x++ )
        {
            $this->page->insert_block( "squarereserve2", array(
                    'X' => $x,
                    'LEFT' => 100,
                    'TOP' => round( (3-$x)*$ver_scale ),
                    
            ) );
                 
        }

        for( $x=4; $x<=6; $x++ )
        {
            $this->page->insert_block( "squarereserve2", array(
                    'X' => $x,
                    'LEFT' => 0,
                    'TOP' => round( (6-$x)*$ver_scale ),
                    
            ) );
                 
        }
        



        }


        if ($current_color == '0000ff')
        {
        $this->page->begin_block( "letscatchthelion_letscatchthelion", "squarereserve1" );
        
        $hor_scale = 100;
        $ver_scale = 100;
        for( $x=1; $x<=3; $x++ )
        {
            $this->page->insert_block( "squarereserve1", array(
                    'X' => $x,
                    'LEFT' => 100,
                    'TOP' => round( (3-$x)*$hor_scale ),
                    
            ) );
                 
        }

        for( $x=4; $x<=6; $x++ )
        {
            $this->page->insert_block( "squarereserve1", array(
                    'X' => $x,
                    'LEFT' => 0,
                    'TOP' => round( (6-$x)*$hor_scale ),
                    
            ) );
                 
        }     
        

        $this->page->begin_block( "letscatchthelion_letscatchthelion", "squarereserve2" );
        
        $hor_scale = 100;
        $ver_scale = 100;
        for( $x=1; $x<=3; $x++ )
        {
            $this->page->insert_block( "squarereserve2", array(
                    'X' => $x,
                    'LEFT' => 0,
                    'TOP' => round( ($x-1)*$hor_scale ),
                    
            ) );
                 
        }

        for( $x=4; $x<=6; $x++ )
        {
            $this->page->insert_block( "squarereserve2", array(
                    'X' => $x,
                    'LEFT' => 100,
                    'TOP' => round( ($x-4)*$hor_scale ),
                    
            ) );
                 
        }



        }


        /*********** Do not change anything below this line  ************/
  	}
}
