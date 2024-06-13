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
  * letscatchthelion.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */
 


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class LetsCatchTheLion extends Table
{
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array( 
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );        
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "letscatchthelion";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( ',', $values );
        self::DbQuery( $sql );
        //self::reattributeColorsBasedOnPreferences( $players, $gameinfos['player_colors'] );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here

        self::initStat( 'player', 'token_captured', 0 ); 
        self::initStat( 'player', 'token_lost', 0 );
        self::initStat( 'player', 'turns_number', 0 );

        self::DbQuery("INSERT INTO lastmove (id, tokenid) VALUES (1, NULL)");


        $sql = "INSERT INTO board (board_x,board_y,board_player,board_type) VALUES ";
        $sql_values = array();
        list( $redplayer_id, $blueplayer_id ) = array_keys( $players );
        for( $x=1; $x<=3; $x++ )
        {
            for( $y=1; $y<=4; $y++ )
            {
                $token_player = "NULL";
                $token_type = "NULL";
                if ($x==1 && $y==1){
                    $token_player = "'$blueplayer_id'";
                    $token_type = 3;
                }
                else if ($x==2 && $y==1){
                    $token_player = "'$blueplayer_id'";
                    $token_type = 4;
                }
                else if ($x==3 && $y==1){
                    $token_player = "'$blueplayer_id'";
                    $token_type = 2;
                }
                else if ($x==2 && $y==2){
                    $token_player = "'$blueplayer_id'";
                    $token_type = 1;
                }
                else if ($x==2 && $y==3){
                    $token_player = "'$redplayer_id'";
                    $token_type = 1;
                }
                else if ($x==1 && $y==4){
                    $token_player = "'$redplayer_id'";
                    $token_type = 2;
                }
                else if ($x==2 && $y==4){
                    $token_player = "'$redplayer_id'";
                    $token_type = 4;
                }
                else if ($x==3 && $y==4){
                    $token_player = "'$redplayer_id'";
                    $token_type = 3;
                }


                $sql_values[] = "('$x','$y',$token_player,$token_type)";
            }
        }
        $sql .= implode( ',', $sql_values );
        self::DbQuery( $sql );


       

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score, player_color color FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        $result['board'] = self::getObjectListFromDB( "SELECT board_x x, board_y y, board_player player, board_type type FROM board WHERE board_player IS NOT NULL" );
        $result['reserve1'] = self::getObjectListFromDB( "SELECT reserve_x x, reserve_type type FROM reserve1 WHERE reserve_type IS NOT NULL" );
        $result['reserve2'] = self::getObjectListFromDB( "SELECT reserve_x x, reserve_type type FROM reserve2 WHERE reserve_type IS NOT NULL" );
        $result['lastmove'] = self::getObjectListFromDB( "SELECT tokenid lastmove FROM lastmove WHERE id = 1 " );
        
        
        
        


        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 50;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    function lion($colorlion, $square_id)  // square_id est le square du lion
    {
        if ($colorlion == '008c00')
        {
            $coloradverse = '0000ff';
        }
        if ($colorlion == '0000ff')
        {
            $coloradverse = '008c00';
        }

        $nosecure = 0;
        $lionplayerid = self::getObjectListFromDB( "SELECT player_id id FROM player WHERE player_color = '{$colorlion}'" )[0]['id'];
        $noactiveplayerid = self::getObjectListFromDB( "SELECT player_id id FROM player WHERE player_color = '{$coloradverse}'" )[0]['id'];
        $positiontokenadverse = self::getObjectListFromDB("SELECT board_x x, board_y y, board_type type FROM board WHERE board_player= {$noactiveplayerid}");
        $posxlion = explode("_", $square_id)[1]; 
        $posylion = explode("_", $square_id)[2];

        foreach ($positiontokenadverse as $test)
        {
            $posxennemi = $test['x'];
            $posyennemi = $test['y'];
            $typeennemi = $test['type'];

            if (($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1) && $posyennemi == $posylion && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5)) //droite et gauche lion girafe et poule
            {
                $nosecure = 1;
            }

            if ($colorlion == '008c00' && $posxennemi == $posxlion  && $posyennemi == $posylion - 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5 || $typeennemi == 1)) // devant rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && $posxennemi == $posxlion  && $posyennemi == $posylion + 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5 || $typeennemi == 1)) // devant bleu
            {
                $nosecure = 1;
            }

            if ($colorlion == '008c00' && $posxennemi == $posxlion  && $posyennemi == $posylion + 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5)) // derriere rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && $posxennemi == $posxlion  && $posyennemi == $posylion - 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5)) // derriere bleu
            {
                $nosecure = 1;
            }

            if ($colorlion == '008c00' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion - 1) && ($typeennemi == 4 || $typeennemi == 2 || $typeennemi == 5)) //diagonnale avant rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion + 1) && ($typeennemi == 4 || $typeennemi == 2 || $typeennemi == 5)) //diagonnale avant bleu
            {
                $nosecure = 1;
            }

            
            if ($colorlion == '008c00' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion + 1) && ($typeennemi == 4 || $typeennemi == 2)) //diagonnale arriere rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion - 1) && ($typeennemi == 4 || $typeennemi == 2)) //diagonnale arriere bleu
            {
                $nosecure = 1;
            }

            

        }
        
        
        return $nosecure;


    }








    function lionsecure($colorlion, $square_id)  // square_id est le square de destination du lion
    {
        if ($colorlion == '008c00')
        {
            $coloradverse = '0000ff';
        }
        if ($colorlion == '0000ff')
        {
            $coloradverse = '008c00';
        }
        
        $nosecure = 0;
        $lionplayerid = self::getObjectListFromDB( "SELECT player_id id FROM player WHERE player_color = '{$colorlion}'" )[0]['id'];
        $noactiveplayerid = self::getObjectListFromDB( "SELECT player_id id FROM player WHERE player_color = '{$coloradverse}'" )[0]['id'];
        $positiontokenadverse = self::getObjectListFromDB("SELECT board_x x, board_y y, board_type type FROM board WHERE board_player= {$noactiveplayerid}");
        $posxlion = explode("_", $square_id)[1]; 
        $posylion = explode("_", $square_id)[2];

        

        foreach ($positiontokenadverse as $test)
        {
            $posxennemi = $test['x'];
            $posyennemi = $test['y'];
            $typeennemi = $test['type'];

            if (($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1) && $posyennemi == $posylion && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5)) //droite et gauche lion girafe et poule
            {
                $nosecure = 1;
            }

            if ($colorlion == '008c00' && $posxennemi == $posxlion  && $posyennemi == $posylion - 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5 || $typeennemi == 1)) // devant rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && $posxennemi == $posxlion  && $posyennemi == $posylion + 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5 || $typeennemi == 1)) // devant bleu
            {
                $nosecure = 1;
            }

            if ($colorlion == '008c00' && $posxennemi == $posxlion  && $posyennemi == $posylion + 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5)) // derriere rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && $posxennemi == $posxlion  && $posyennemi == $posylion - 1 && ($typeennemi == 4 || $typeennemi == 3 || $typeennemi == 5)) // derriere bleu
            {
                $nosecure = 1;
            }

            if ($colorlion == '008c00' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion - 1) && ($typeennemi == 4 || $typeennemi == 2 || $typeennemi == 5)) //diagonnale avant rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion + 1) && ($typeennemi == 4 || $typeennemi == 2 || $typeennemi == 5)) //diagonnale avant bleu
            {
                $nosecure = 1;
            }

            
            if ($colorlion == '008c00' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion + 1) && ($typeennemi == 4 || $typeennemi == 2)) //diagonnale arriere rouge
            {
                $nosecure = 1;
            }

            if ($colorlion == '0000ff' && ($posxennemi == $posxlion - 1 || $posxennemi == $posxlion + 1)  && ($posyennemi == $posylion - 1) && ($typeennemi == 4 || $typeennemi == 2)) //diagonnale arriere bleu
            {
                $nosecure = 1;
            }

            

        }
        
        
        return $nosecure;

        
        


    }



//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in letscatchthelion.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */
    function actForfeit()
    {
        $this->gamestate->nextState( 'validate' );

    }

    function actYes()
    {
        $player_id = self::getActivePlayerId();
        self::DbQuery( "UPDATE player set player_score = 1 WHERE player_id != {$player_id}" );
                        $newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "forfeit", clienttranslate('${player_name} forfeits'),
                        array(
               
                        'score' => $newScore,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name from player WHERE player_id = {$player_id}"),
                        
                        
                        )
                        );
        $this->gamestate->nextState( 'next' );

    }

    function actNo()
    {
        
        $this->gamestate->nextState( 'same' );

    }


    

    function actSelect($token_id, $square_id )
    {
        self::checkAction( 'select' );

            

        $player_id = self::getActivePlayerId();
        $activecolor = self::getObjectListFromDB( "SELECT player_color color FROM player WHERE player_id = {$player_id}" );
        $playeractive_color = $activecolor[0]['color'];
        $selectfromreserve = explode("_", $token_id)[0];

        if ($selectfromreserve != 'tokenreserve1' && $selectfromreserve != 'tokenreserve2') // si le joueur ne selectionne pas le token de sa reserve

        {        
         
        $xpostoken = explode("_", $token_id)[1]; 
        $ypostoken = explode("_", $token_id)[2];
        $xpossquare = explode("_", $square_id)[1]; 
        $ypossquare = explode("_", $square_id)[2];
        $null = "NULL";
        
    

        
        
        $recuptype = self::getObjectListFromDB( "SELECT board_type type FROM board WHERE board_x = {$xpostoken} AND board_y = {$ypostoken} ", true )[0];
        $recupplayer = self::getObjectListFromDB( "SELECT board_player player FROM board WHERE board_x = {$xpostoken} AND board_y = {$ypostoken} ", true )[0];

        $other = self::getObjectListFromDB( "SELECT board_player player, board_type type FROM board where board_x = {$xpossquare} and board_y = {$ypossquare}");
        $otherplayer = $other[0]['player'];
        
        
        
      
        
        if ($otherplayer === NULL) // il n'y a pas d'ennemi
            {
                
                
                // test si le lion atteint la derniere ligne
                if($recuptype == 4 && $playeractive_color == "008c00" && ($square_id == "square_1_1" || $square_id == "square_2_1" || $square_id == "square_3_1"))
                {
                    
                    self::DbQuery( "UPDATE player set player_lion = 1 WHERE player_color = '{$playeractive_color}'" );
                        /*$newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", '',
                        array(
               
                        'score' => $newScore,
                        'player_name' => self::getUniqueValueFromDB("select player_name from player where player_id={$player_id}"),
                        'type' => clienttranslate($this->types[$recuptype]),
                        
                        )
                        );*/
                }

                
                if($recuptype == 4 && $playeractive_color == "0000ff" && ($square_id == "square_1_4" || $square_id == "square_2_4" || $square_id == "square_3_4"))
                {
                    
                        self::DbQuery( "UPDATE player set player_lion = 1 WHERE player_color = '{$playeractive_color}'" );
                        /*$newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", '',
                        array(
               
                        'score' => $newScore,
                        'player_name' => self::getUniqueValueFromDB("select player_name from player where player_id={$player_id}"),
                        'type' => clienttranslate($this->types[$recuptype]),
                        
                        )
                        );*/
                }

                // test si le poussin atteint la derniere ligne
                if($recuptype == 1 && ($square_id == "square_1_1" || $square_id == "square_2_1" || $square_id == "square_3_1" || $square_id == "square_1_4" || $square_id == "square_2_4" || $square_id == "square_3_4"))
                {
                    $recuptypeafter = 5;
                    self::DbQuery( "UPDATE board set board_player = {$recupplayer}, board_type = {$recuptypeafter} WHERE board_x = {$xpossquare} AND board_y = {$ypossquare}" );
                    self::DbQuery( "UPDATE board set board_player = {$null}, board_type = {$null} WHERE board_x = {$xpostoken} AND board_y = {$ypostoken}" );
                    $this->notifyAllPlayers( "move", clienttranslate('${player_name} moves the chick to ${coord} and turns it into a hen'),
                    array(
                        'i18n' => array( 'coord' ),
                        'mobile' => "token_".$xpostoken."_".$ypostoken,
                        'parent' => "square_".$xpossquare."_".$ypossquare,
                        'movetype' => $recuptype,
                        'movetypeafter' => $recuptypeafter,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'coord' => $this->coords[$square_id],
                        
                    )
                );
                }

                else
                {
                    $recuptypeafter = $recuptype;
                    self::DbQuery( "UPDATE board set board_player = {$recupplayer}, board_type = {$recuptypeafter} WHERE board_x = {$xpossquare} AND board_y = {$ypossquare}" );
                    self::DbQuery( "UPDATE board set board_player = {$null}, board_type = {$null} WHERE board_x = {$xpostoken} AND board_y = {$ypostoken}" );
                    $this->notifyAllPlayers( "move", clienttranslate('${player_name} moves ${type} to ${coord}'),
                        array(
                        'i18n' => array( 'type', 'coord' ),
                        'mobile' => "token_".$xpostoken."_".$ypostoken,
                        'parent' => "square_".$xpossquare."_".$ypossquare,
                        'movetype' => $recuptype,
                        'movetypeafter' => $recuptypeafter,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'type' => $this->types[$recuptypeafter],
                        'coord' => $this->coords[$square_id],
                        
                        )
                        );

                }
                


            }
        
        
       else //il y a un ennemi
            {  
                
                // test si le lion atteint la derniere ligne
                if($recuptype == 4 && $playeractive_color == "008c00" && ($square_id == "square_1_1" || $square_id == "square_2_1" || $square_id == "square_3_1"))
                {
                    
                    self::DbQuery( "UPDATE player set player_lion = 1 WHERE player_color = '{$playeractive_color}'" );
                        /*$newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", '',
                        array(
               
                        'score' => $newScore,
                        
                        )
                        );*/
                }

                if($recuptype == 4 && $playeractive_color == "0000ff" && ($square_id == "square_1_4" || $square_id == "square_2_4" || $square_id == "square_3_4"))
                {
                    
                    self::DbQuery( "UPDATE player set player_lion = 1 WHERE player_color = '{$playeractive_color}'" );
                        /*$newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", '',
                        array(
               
                        'score' => $newScore,
                        
                        )
                        );*/
                }

                // test si le poussin atteint la derniere ligne
                if($recuptype == 1 && ($square_id == "square_1_1" || $square_id == "square_2_1" || $square_id == "square_3_1" || $square_id == "square_1_4" || $square_id == "square_2_4" || $square_id == "square_3_4"))
                {
                    $recuptypeafter = 5;
                }
                else
                {
                    $recuptypeafter = $recuptype;

                }

                              
                $typekill = $other[0]['type'];

                // test si la poule se fait tuer
                if($typekill == 5 )
                {
                    $typekillafter = 1;
                }
                else
                {
                    $typekillafter = $typekill;
                }

                self::DbQuery( "UPDATE board set board_player = {$recupplayer}, board_type = {$recuptypeafter} WHERE board_x = {$xpossquare} AND board_y = {$ypossquare}" );
                self::DbQuery( "UPDATE board set board_player = {$null}, board_type = {$null} WHERE board_x = {$xpostoken} AND board_y = {$ypostoken}" );
                
                
                // test si le lion se fait tuer
                if ($typekillafter == 4)
                    {
                        self::DbQuery( "UPDATE player set player_score = 1 WHERE player_color = '{$playeractive_color}'" );
                        $newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", '',
                        array(
               
                        'score' => $newScore,
                        
                        )
                        );
                    }
                
                if($recuptype == 1 && $recuptypeafter == 5)
                {
                if ($playeractive_color == "008c00")
                {
                    $nbrelignereserve = self::getUniqueValueFromDB("SELECT count(*) FROM reserve1");
                    $a = $nbrelignereserve + 1;
                    self::DbQuery( "INSERT reserve1 (reserve_x, reserve_type) VALUE ({$a}, {$typekillafter})");
                    $nbrelignereserve1 = self::getUniqueValueFromDB("SELECT count(*) FROM reserve1");
                    $this->notifyAllPlayers( "move", clienttranslate('${player_name} moves the chick to ${coord}, turns it into a hen and captures ${captured}'),
                    array(
                        'i18n' => array( 'coord', 'captured' ),
                        'mobile' => "token_".$xpostoken."_".$ypostoken,
                        'parent' => "square_".$xpossquare."_".$ypossquare,
                        'reserve' => 1,
                        'color' => '008c00',
                        'typekill' => $typekill,
                        'typekillafter' => $typekillafter,
                        'nbrelignereserve' => $nbrelignereserve1,
                        'movetype' => $recuptype,
                        'movetypeafter' => $recuptypeafter,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'captured' => $this->types[$typekill],
                        'coord' => $this->coords[$square_id],
                    )
                    );

                }

                if ($playeractive_color == "0000ff")
                {
                    $nbrelignereserve = self::getUniqueValueFromDB("SELECT count(*) FROM reserve2");
                    $a = $nbrelignereserve + 1;
                    self::DbQuery( "INSERT reserve2 (reserve_x, reserve_type) VALUE ({$a}, {$typekillafter})");
                    $nbrelignereserve2 = self::getUniqueValueFromDB("SELECT count(*) FROM reserve2");
                    $this->notifyAllPlayers( "move", clienttranslate('${player_name} moves the chick to ${coord}, turns it into a hen and captures ${captured}'),
                    array(
                        'i18n' => array( 'coord', 'captured' ),
                        'mobile' => "token_".$xpostoken."_".$ypostoken,
                        'parent' => "square_".$xpossquare."_".$ypossquare,
                        'reserve' => 2,
                        'color' => '0000ff',
                        'typekill' => $typekill,
                        'typekillafter' => $typekillafter,
                        'nbrelignereserve' => $nbrelignereserve2,
                        'movetype' => $recuptype,
                        'movetypeafter' => $recuptypeafter,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'captured' => $this->types[$typekill],
                        'coord' => $this->coords[$square_id],
                    )
                    );

                }
                }

                else
                {
                if ($playeractive_color == "008c00")
                {
                    $nbrelignereserve = self::getUniqueValueFromDB("SELECT count(*) FROM reserve1");
                    $a = $nbrelignereserve + 1;
                    self::DbQuery( "INSERT reserve1 (reserve_x, reserve_type) VALUE ({$a}, {$typekillafter})");
                    $nbrelignereserve1 = self::getUniqueValueFromDB("SELECT count(*) FROM reserve1");
                    $this->notifyAllPlayers( "move", clienttranslate('${player_name} moves ${type} to ${coord} and captures ${captured}'),
                    array(
                        'i18n' => array( 'type', 'coord', 'captured' ),
                        'mobile' => "token_".$xpostoken."_".$ypostoken,
                        'parent' => "square_".$xpossquare."_".$ypossquare,
                        'reserve' => 1,
                        'color' => '008c00',
                        'typekill' => $typekill,
                        'typekillafter' => $typekillafter,
                        'nbrelignereserve' => $nbrelignereserve1,
                        'movetype' => $recuptype,
                        'movetypeafter' => $recuptypeafter,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'type' => $this->types[$recuptypeafter],
                        'captured' => $this->types[$typekill],
                        'coord' => $this->coords[$square_id],
                    )
                    );

                }

                if ($playeractive_color == "0000ff")
                {
                    $nbrelignereserve = self::getUniqueValueFromDB("SELECT count(*) FROM reserve2");
                    $a = $nbrelignereserve + 1;
                    self::DbQuery( "INSERT reserve2 (reserve_x, reserve_type) VALUE ({$a}, {$typekillafter})");
                    $nbrelignereserve2 = self::getUniqueValueFromDB("SELECT count(*) FROM reserve2");
                    $this->notifyAllPlayers( "move", clienttranslate('${player_name} moves ${type} to ${coord} and captures ${captured}'),
                    array(
                        'i18n' => array( 'type', 'coord', 'captured' ),
                        'mobile' => "token_".$xpostoken."_".$ypostoken,
                        'parent' => "square_".$xpossquare."_".$ypossquare,
                        'reserve' => 2,
                        'color' => '0000ff',
                        'typekill' => $typekill,
                        'typekillafter' => $typekillafter,
                        'nbrelignereserve' => $nbrelignereserve2,
                        'movetype' => $recuptype,
                        'movetypeafter' => $recuptypeafter,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'type' => $this->types[$recuptypeafter],
                        'captured' => $this->types[$typekill],
                        'coord' => $this->coords[$square_id],
                    )
                    );

                }
                }

                $this->incStat(1, 'token_captured', $player_id);
                $this->incStat(1, 'token_lost', $otherplayer);   
          
            }

        }

        else {  // si le joueur selectionne un token de sa réserve et click sur un square libre

        $idtokenreserve = explode("_", $token_id)[0]; //tokenreserve1 ou tokenreserve2
        $postokenreserve = explode("_", $token_id)[1]; //position dans la reserve
        $xpossquare = explode("_", $square_id)[1]; 
        $ypossquare = explode("_", $square_id)[2];
        $null = "NULL";

        

        if ($idtokenreserve == 'tokenreserve1'){
            
            $nbrelignereserve = self::getUniqueValueFromDB("SELECT count(*) FROM reserve1"); 
            $recuptypetoken = self::getObjectListFromDB( "SELECT reserve_type type FROM reserve1 WHERE reserve_x = {$postokenreserve} ", true )[0];
            self::DbQuery( "UPDATE board set board_player = {$player_id}, board_type = {$recuptypetoken} WHERE board_x = {$xpossquare} AND board_y = {$ypossquare}" );
            
            self::DbQuery( "DELETE FROM reserve1 WHERE reserve_x = {$postokenreserve}" ); //je supprime la ligne du token

            for ($position = $postokenreserve + 1; $position <= $nbrelignereserve; $position++)
            {
                $set = $position - 1;
                self::DbQuery( "UPDATE reserve1 set reserve_x = {$set} WHERE reserve_x = {$position}" );
            }

            


        }
        
        if ($idtokenreserve == 'tokenreserve2'){

            $nbrelignereserve = self::getUniqueValueFromDB("SELECT count(*) FROM reserve2"); 
            $recuptypetoken = self::getObjectListFromDB( "SELECT reserve_type type FROM reserve2 WHERE reserve_x = {$postokenreserve} ", true )[0];
            self::DbQuery( "UPDATE board set board_player = {$player_id}, board_type = {$recuptypetoken} WHERE board_x = {$xpossquare} AND board_y = {$ypossquare}" );

            self::DbQuery( "DELETE FROM reserve2 WHERE reserve_x = {$postokenreserve}" ); //je supprime la ligne du token

            for ($position = $postokenreserve + 1; $position <= $nbrelignereserve; $position++)
            {
                $set = $position - 1;
                self::DbQuery( "UPDATE reserve2 set reserve_x = {$set} WHERE reserve_x = {$position}" );
            }

            
        
        }

        $this->notifyAllPlayers( "movefromreserve", clienttranslate('${player_name} places ${type} to ${coord} from the reserve'),
                    array(
                        'i18n' => array( 'type', 'coord'),
                        'mobile' => $token_id,
                        'parent' => $square_id,
                        'position' => $postokenreserve,
                        'nombre' => $nbrelignereserve,
                        'tokenreserve' => $selectfromreserve, //tokenreserve1 ou tokenreserve2
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        'type' => $this->types[$recuptypetoken],
                        'coord' => $this->coords[$square_id],


                        
                    )
                    );


        }
        
        
        $xlastmove = explode("_", $square_id)[1]; 
        $ylastmove = explode("_", $square_id)[2];
        $lastmove = 'token_'.$xlastmove.'_'.$ylastmove;
        self::DbQuery( "UPDATE lastmove set tokenid = '{$lastmove}' WHERE id = 1 " );

        $this->gamestate->nextState( 'next' );

    }


    

    
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */
    
    function argPlayerValidate()
    {
        $ret = array();
        return $ret;
    }
  
    function argPlayerTurn()
    {
       
        $ret = array();
        $ret["selectable"] = array();
        $ret["lastmove"] = array();
        $player_id = self::getActivePlayerId();

        $activecolor = self::getObjectListFromDB( "SELECT player_color color FROM player WHERE player_id = {$player_id}" );
        $playeractive_color = $activecolor[0]['color'];
        
        
        $sql = "SELECT board_x x, board_y y, board_type type FROM board WHERE board_type IS NOT NULL AND board_player = {$player_id}";
        $board = self::getObjectListFromDB( $sql );
        foreach ($board as $variable)
        {
        $x = $variable['x'];
        $y = $variable['y'];
        $oldx = $x;
        $oldy = $y;
        
        $directions = array(
            array( -1,-1 ), array( -1,0 ), array( -1, 1 ), array( 0, -1),
            array( 0,1 ), array( 1,-1), array( 1,0 ), array( 1, 1 )
        );

        if(($variable['type']==1)&&($playeractive_color == "008c00"))
            {
                $directions = array(
                  array( 0,-1 )
                );
            }

        if(($variable['type']==1)&&($playeractive_color == "0000ff"))
            {
                $directions = array(
                  array( 0,1 )
                );
            }

        if(($variable['type']==5)&&($playeractive_color == "008c00"))
            {
                $directions = array(
                    array( -1,-1 ), array( 0,-1 ), array( 1,-1 ), array( -1,0 ), array( 1,0 ), array( 0,1 )
                );
            }

        if(($variable['type']==5)&&($playeractive_color == "0000ff"))
            {
                $directions = array(
                    array( -1,1 ), array( 0,1 ), array( 1,1 ), array( -1,0 ), array( 1,0 ), array( 0,-1 )
                );
            }

        if($variable['type']==2)
        {
            $directions = array(
                array( -1,-1 ),  array( -1, 1 ), array( 1,-1), array( 1, 1 )
            );
        }

        if($variable['type']==3)
        {
            $directions = array(
                array( -1,0 ), array( 0, -1), array( 0,1 ), array( 1,0 )
            );
        }

        if($variable['type']==4)
        {
            
            //$positionlion = self::getObjectListFromDB( "SELECT board_x x, board_y y FROM board WHERE board_type = 4 AND board_player = {$player_id}");
            //$directions = array();
            $possibledirection = array(
                array( -1,-1 ), array( -1,0 ), array( -1, 1 ), array( 0, -1), array( 0,1 ), array( 1,-1), array( 1,0 ), array( 1, 1 )
            );
            /*foreach( $possibledirection as $testsecure )
            {
                $possiblex = $positionlion[0]['x'] + $testsecure[0];
                $possibley = $positionlion[0]['y'] + $testsecure[1];
                $square = 'square_'.($possiblex).'_'.($possibley);
                $secure = $this->lionsecure($playeractive_color, $square);
                if ($secure == 0)
                {
                    $directions[] = array($testsecure[0],$testsecure[1]);
                }

            }*/


        }


        foreach( $directions as $direction )
            {
                $newx = $oldx+$direction[0];
                $newy = $oldy+$direction[1];

                if ( $newx>=1 && $newx<=3 && $newy>=1 && $newy<=4 && self::getUniqueValueFromDB("SELECT count(*) FROM board WHERE board_player = {$player_id} AND board_x = {$newx} AND board_y = {$newy}")==0)
                {
                    $ret["selectable"]['token_'.$x.'_'.$y][] = 'square_'.($newx).'_'.($newy) ;
                    

                }
            }

                    
        }

        // gestion du deplacement à partir de la reserve
       
        $casesvides = self::getObjectListFromDB( "SELECT board_x xvide, board_y yvide FROM board WHERE board_player IS NULL" );
        foreach( $casesvides as $casevide)
                {
                    $xvide = $casevide['xvide'];
                    $yvide = $casevide['yvide'];

                    if ($playeractive_color == "008c00")
                    {
                        $tokensreserve1 = self::getObjectListFromDB( "SELECT reserve_x token FROM reserve1" );
                        foreach ($tokensreserve1 as $tokenreserve1)

                        {
                            $rid = $tokenreserve1['token'];
                            $ret["selectable"]['tokenreserve1_'.$rid][] = 'square_'.($xvide).'_'.($yvide);
                            
                        }


                    }


                    if ($playeractive_color == "0000ff")
                    {
                        $tokensreserve2 = self::getObjectListFromDB( "SELECT reserve_x token FROM reserve2" );
                        foreach ($tokensreserve2 as $tokenreserve2)

                        {
                            $rid = $tokenreserve2['token'];
                            $ret["selectable"]['tokenreserve2_'.$rid][] = 'square_'.($xvide).'_'.($yvide);

                        }


                    }


                }
       
        $last = self::getObjectListFromDB( "SELECT tokenid last FROM lastmove WHERE id = 1" );
        $ret["lastmove"][] = $last[0]['last'];
        
            
        
        return $ret;
        
    
    }
    


    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */
    

    function stNextPlayer()
    {
        
        $this->incStat(1, 'turns_number',self::getActivePlayerId());
        $playeractive = self::getActivePlayerId();
        $activecolor = self::getObjectListFromDB( "SELECT player_color color FROM player WHERE player_id = {$playeractive}" );
        $playeractive_color = $activecolor[0]['color'];
        $testwin = self::getObjectListFromDB( "SELECT player_score score FROM player WHERE player_score = '1'", true );
        $secondtestwin = self::getObjectListFromDB( "SELECT player_lion lion FROM player WHERE player_id = '{$playeractive}'", true );
        $secondtestwinotherplayer = self::getObjectListFromDB( "SELECT player_lion lion FROM player WHERE player_id != '{$playeractive}'", true );
        
        
        $positionlion = self::getObjectListFromDB( "SELECT board_x x, board_y y FROM board WHERE board_type = '4' AND board_player = {$playeractive}" );
        
        $squareoflion = 'square_'.$positionlion[0]['x'].'_'.$positionlion[0]['y'];
        $nosecure = $this->lion($playeractive_color, $squareoflion);
        
        if ($testwin == NULL)
        {
            

        if($secondtestwinotherplayer[0] == 2)
        {

            
         
        self::DbQuery( "UPDATE player set player_score = 1 WHERE player_id != '{$playeractive}'" );
                        $newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", clienttranslate('${player_name} saves the lion'),
                        array(
               
                        'score' => $newScore,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id!={$playeractive}"),
                        
                        )
                        );
            $this->gamestate->nextState( 'endGame' );
        

        }
        
               
        if ($secondtestwin[0] == 1 && $nosecure == 0 && $secondtestwinotherplayer[0] != 2)

        {

            self::DbQuery( "UPDATE player set player_score = 1 WHERE player_id = '{$playeractive}'" );
                        $newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", clienttranslate('${player_name} saves the lion'),
                        array(
               
                        'score' => $newScore,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$playeractive}"),
                        
                        )
                        );
            $this->gamestate->nextState( 'endGame' );



        }


        if ($secondtestwin[0] == 1 && $nosecure == 1)

        {

            self::DbQuery( "UPDATE player set player_lion = 2 WHERE player_id = '{$playeractive}'" );
            




        }

        }

        

        if ($testwin)
        {
            $this->gamestate->nextState( 'endGame' );
        }

        else
        {
        // Active next player
        $player_id = self::activeNextPlayer();
        

        $testmvt = array(); // je vais tester si le joueur suivant est bloqué
        $testmvt = $this->argPlayerTurn();  
        if (empty($testmvt["selectable"])) // si le tableau est vide c'est que le joueur suivant n'a aucune possibilité de mouvement
        {
        self::DbQuery( "UPDATE player set player_score = 1 WHERE player_id != '{$player_id}'" );
                        $newScore = self::getCollectionFromDb( "SELECT player_id, player_score FROM player", true );
                        $this->notifyAllPlayers( "score", clienttranslate('${player_name} is blocked'),
                        array(
               
                        'score' => $newScore,
                        'player_name' => self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id={$player_id}"),
                        
                        )
                        );
            $this->gamestate->nextState( 'endGame' );
        }
        
        // This player can play. Give him some extra time
        self::giveExtraTime( $player_id );
        $this->gamestate->nextState( 'next' );
        }
        
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//



    if( $from_version <= 2309061320 )
    {

    $sql = "UPDATE DBPREFIX_player SET player_color = '008c00' WHERE player_color = 'ff0000'";
    self::applyDbUpgradeToAllDB( $sql );
    }


    if( $from_version <= 2309031908 )
    {
    
    $sql = "ALTER TABLE DBPREFIX_player ADD `player_lion` int(2) unsigned DEFAULT NULL";
    self::applyDbUpgradeToAllDB( $sql );
    }

    }    
}
