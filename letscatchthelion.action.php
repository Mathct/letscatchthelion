<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * LetsCatchTheLion implementation : © <Mathieu Chatrain> <mathieu.chatrain@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * letscatchthelion.action.php
 *
 * LetsCatchTheLion main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/letscatchthelion/letscatchthelion/myAction.html", ...)
 *
 */
  
  
  class action_letscatchthelion extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "letscatchthelion_letscatchthelion";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
  	// TODO: defines your action entry points there

    public function actSelect()
  	{
  	    self::setAjaxMode();
  	    
  	    $arg1 = self::getArg( "arg1", AT_alphanum );
  	    $arg2 = self::getArg( "arg2", AT_alphanum );
  	    
  	    $this->game->actSelect( $arg1, $arg2 );
  	    
  	    self::ajaxResponse( );
  	}

    public function actForfeit()
  	{
		self::setAjaxMode();
  	$this->game->actForfeit( );
		self::ajaxResponse( );
  	}

    public function actYes()
  	{
		self::setAjaxMode();
  	$this->game->actYes( );
		self::ajaxResponse( );
  	}

    public function actNo()
  	{
		self::setAjaxMode();
  	$this->game->actNo( );
		self::ajaxResponse( );
  	}

    

    
    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

  }
  

