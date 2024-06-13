/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * LetsCatchTheLion implementation : © <Mathieu Chatrain> <mathieu.chatrain@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * letscatchthelion.js
 *
 * LetsCatchTheLion user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.letscatchthelion", ebg.core.gamegui, {
        constructor: function(){
            console.log('letscatchthelion constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;

        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
            setup: function( gamedatas )
            {
                console.log( "Starting game setup" );
    
    
    
                
                // Setting up player boards
                for( var player_id in gamedatas.players )
                {
                    var player = gamedatas.players[player_id];
                             
                    // TODO: Setting up players boards if needed
                }
                
                // TODO: Set up your game interface here, according to "gamedatas"
    
    
                for( var i in gamedatas.board )
                {
                    var variable = gamedatas.board[i];
                    
                    if( variable.player !== null )
                    {
                        this.addTokenOnBoard( variable.x, variable.y, variable.type, variable.player );
                    }
                }
    
                for( var i in gamedatas.reserve1 )
                {
                    var variable = gamedatas.reserve1[i];
                    var color = "008c00";
                    
                    if( variable.type !== null )
                    {
                        this.addTokenOnReserve( variable.x, 1, variable.type, color );
                    }
                }
    
                for( var i in gamedatas.reserve2 )
                {
                    var variable = gamedatas.reserve2[i];
                    var color = "0000ff";
                    
                    if( variable.type !== null )
                    {
                        this.addTokenOnReserve( variable.x, 2, variable.type, color );
                    }
                }
    
                if(!this.isSpectator)
				{
                if(this.gamedatas.players[this.player_id].color =='0000ff')
                {
                    dojo.query("#plateau").addClass("plateaubleu");
                    dojo.query(".token").addClass("rotate180deg");
                    dojo.query("#reserve1").addClass("reserve1");
                    dojo.query("#reserve2").addClass("reserve2");
                }
                }

                
     
                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();
                dojo.query(".token").connect('onclick', this, 'onSelect' );
                dojo.query(".square").connect('onclick', this, 'onSelectsquare' );
                
                
                
                
    
                console.log( "Ending game setup" );
            },
           
    
            ///////////////////////////////////////////////////
            //// Game & client states
            
            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function( stateName, args )
            {
                dojo.query(".selectable").removeClass("selectable");
                dojo.query(".selected").removeClass("selected");
                dojo.query(".colorgreen").removeClass("colorgreen");
                dojo.query(".colorblue").removeClass("colorblue");
                dojo.query(".lastmove").removeClass("lastmove");
                
                
               
                switch( stateName )
                {
                
                case 'playerTurn':

                if(args.args.lastmove[0] != null)
                {
                    var enfant = document.getElementById(args.args.lastmove);
                    var idDuParent = enfant.parentNode.id;
                    dojo.query("#"+idDuParent).addClass("lastmove");
                }

                break;

                }

                
               
    
    
                console.log( 'Entering state: '+stateName );
                
                if( this.isCurrentPlayerActive() && this.gamedatas.players[this.player_id].color =='008c00')
                {
                switch( stateName )
                {
                
                case 'playerTurn':

                
                this.args = args.args;
                for( var sid in this.args.selectable )
                    {
                        dojo.query("#"+sid).addClass("selectable");
                        var enfant = document.getElementById(sid);
                        var idDuParent = enfant.parentNode.id;
                        dojo.query("#"+idDuParent).addClass("colorgreen");
                    }
                break;
    
                case 'client_playerTurn': 
                    var enfant = document.getElementById(this.selected);
                    var idDuParent = enfant.parentNode.id;
                    dojo.query("#"+idDuParent).addClass("selected");
                for( var sid in this.args.selectable[this.selected] )
                    {
                        dojo.query("#"+this.args.selectable[this.selected][sid]).addClass("selectable");
                        dojo.query("#"+this.args.selectable[this.selected][sid]).addClass("colorgreen");
                        
                    }
                break;
    
                
                case 'dummmy':
                    break;
                }
                }   
    
                if( this.isCurrentPlayerActive() && this.gamedatas.players[this.player_id].color =='0000ff')
                {
                switch( stateName )
                {
                
                case 'playerTurn':
                this.args = args.args;
                for( var sid in this.args.selectable )
                    {
                        dojo.query("#"+sid).addClass("selectable");
                        var enfant = document.getElementById(sid);
                        var idDuParent = enfant.parentNode.id;
                        dojo.query("#"+idDuParent).addClass("colorblue");
                    }
                break;
    
                case 'client_playerTurn': 
                var enfant = document.getElementById(this.selected);
                var idDuParent = enfant.parentNode.id;
                dojo.query("#"+idDuParent).addClass("selected");
                for( var sid in this.args.selectable[this.selected] )
                    {
                        dojo.query("#"+this.args.selectable[this.selected][sid]).addClass("selectable");
                        dojo.query("#"+this.args.selectable[this.selected][sid]).addClass("colorblue");
                    }
                break;
    
                
                case 'dummmy':
                    break;
                }
                }  
    
    
               
    
            },
    
            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function( stateName )
            {
                console.log( 'Leaving state: '+stateName );
    
                
                
                switch( stateName )
                {
    
    
            
                
                /* Example:
                
                case 'myGameState':
                
                    // Hide the HTML block we are displaying only during this game state
                    dojo.style( 'my_html_block_id', 'display', 'none' );
                    
                    break;
               */
               
               
                case 'dummmy':
                    break;
                }               
            }, 
    
            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //        
            onUpdateActionButtons: function( stateName, args )
            {
                console.log( 'onUpdateActionButtons: '+stateName );
                          
                if( this.isCurrentPlayerActive() )
                {            
                    switch( stateName )
                    {
    /*               
                     Example:
     
                     case 'myGameState':
                        
                        // Add 3 action buttons in the action status bar:
                        
                        this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                        this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                        this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                        break;
    */
                    
                        case "playerTurn":
                            this.addActionButton( 'forfeit', _("Forfeit") ,'onOpForfeit', null, null, 'gray' );
                        break;
                    
                        case "client_playerTurn":
                        this.addActionButton( 'cancel', _("Cancel") ,'onOpCancel', null, null, 'gray' );
                        break;

                        case "playerValidateForfeit":
                            this.addActionButton( 'yes', _("Yes") ,'onOpYes', null, null, 'gray' );
                            this.addActionButton( 'no', _("No") ,'onOpNo', null, null, 'gray' );
                        break;
    
    
                    }
                }
            },    
            
            onOpForfeit: function(evt)
            {
                this.ajaxcall( "/letscatchthelion/letscatchthelion/actForfeit.html", { 
                    lock: true,
                             
                    }, 
                    this, function( result ) {}, function( is_error) {} );
    
            }, 
    
            onOpCancel: function(evt)
            {
                dojo.query(".selectable").removeClass("selectable");
                dojo.query(".selected").removeClass("selected");
                dojo.query(".colorgreen").removeClass("colorgreen");
                dojo.query(".colorblue").removeClass("colorblue");
                dojo.query(".lastmove").removeClass("lastmove");
                this.restoreServerGameState();
    
            },

            onOpYes: function(evt)
            {
                this.ajaxcall( "/letscatchthelion/letscatchthelion/actYes.html", { 
                    lock: true,
                             
                    }, 
                    this, function( result ) {}, function( is_error) {} );
    
            },
            
            onOpNo: function(evt)
            {
                this.ajaxcall( "/letscatchthelion/letscatchthelion/actNo.html", { 
                    lock: true,
                             
                    }, 
                    this, function( result ) {}, function( is_error) {} );
    
            },


    
            ///////////////////////////////////////////////////
            //// Utility methods
            
            /*
            
                Here, you can defines some utility methods that you can use everywhere in your javascript
                script.
            
            */
    
                addTokenOnBoard: function( x, y, z, player )  // fonction qui permet de positionner les tokens dans les square (sera utilisé à l'initialisation du borad plus haut dans le JS)
                {
                    dojo.place( this.format_block( 'jstpl_token', {
                        x_y: x+'_'+y,
                        color: this.gamedatas.players[ player ].color,
                        type: z
                    } ) , 'square_'+x+'_'+y );  // j'envoi le div token dans le div qui a l'id square_x_y ca permet d'avoir mon token dans le square et non le token dans le board (dans div tokens)
                    
                    
                },
    
                addTokenOnReserve: function( x, r, z, color )
                {
                    
                    dojo.place( this.format_block( 'jstpl_tokenreserve'+r, {
                        x: x,
                        type: z,
                        color: color,
                    } ) , 'squarereserve'+r+'_'+x );
    
                    
                    
                    
                },
    
                
    
                
                attachToNewParentNoDestroy: function (mobile_in, new_parent_in, relation, place_position) {
    
                    const mobile = $(mobile_in);
                    const new_parent = $(new_parent_in);
        
                    var src = dojo.position(mobile);
                    if (place_position)
                        mobile.style.position = place_position;
                    dojo.place(mobile, new_parent, relation);
                    mobile.offsetTop;//force re-flow
                    var tgt = dojo.position(mobile);
                    var box = dojo.marginBox(mobile);
                    var cbox = dojo.contentBox(mobile);
                    var left = box.l + src.x - tgt.x;
                    var top = box.t + src.y - tgt.y;
        
                    mobile.style.position = "absolute";
                    mobile.style.left = left + "px";
                    mobile.style.top = top + "px";
                    box.l += box.w - cbox.w;
                    box.t += box.h - cbox.h;
                    mobile.offsetTop;//force re-flow
                    return box;
                },
    
            ///////////////////////////////////////////////////
            //// Player's action
            
            /*
            
                Here, you are defining methods to handle player's action (ex: results of mouse click on 
                game objects).
                
                Most of the time, these methods:
                _ check the action is possible at this game state.
                _ make a call to the game server
            
            */
            
            /* Example:
            
            onMyMethodToCall1: function( evt )
            {
                console.log( 'onMyMethodToCall1' );
                
                // Preventing default browser reaction
                dojo.stopEvent( evt );
    
                // Check that this action is possible (see "possibleactions" in states.inc.php)
                if( ! this.checkAction( 'myAction' ) )
                {   return; }
    
                this.ajaxcall( "/backtest/backtest/myAction.html", { 
                                                                        lock: true, 
                                                                        myArgument1: arg1, 
                                                                        myArgument2: arg2,
                                                                        ...
                                                                     }, 
                             this, function( result ) {
                                
                                // What to do after the server call if it succeeded
                                // (most of the time: nothing)
                                
                             }, function( is_error) {
    
                                // What to do after the server call in anyway (success or failure)
                                // (most of the time: nothing)
    
                             } );        
            },        
            
            */
    
            onSelect: function(evt)
            {        	 
                // Preventing default browser reaction
                 dojo.stopEvent( evt );
    
                
                 
                if( !this.isCurrentPlayerActive() || !(evt.currentTarget.classList.contains('selectable')) )
                {   
                    return; 
                }
                 
                this.selected = evt.currentTarget.id;
                this.setClientState("client_playerTurn", {descriptionmyturn: _("${you} must move your piece"),});
            },
    
            onSelectsquare: function(evt)
            {        	 
                         
                 // Preventing default browser reaction
                 dojo.stopEvent( evt );
                 
                if( !this.isCurrentPlayerActive() || (!(evt.currentTarget.classList.contains('selectable')) && !(evt.currentTarget.classList.contains('selectable2'))) )
                {   return; 
                }
                 
                        
    
                this.ajaxcall( "/letscatchthelion/letscatchthelion/actSelect.html", { 
                    lock: true,
                    arg1: this.selected,
                    arg2: evt.currentTarget.id
                 }, 
                 this, function( result ) {}, function( is_error) {} );
    
                 
            
            },
    
            
            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications
    
            /*
                setupNotifications:
                
                In this method, you associate each of your game notifications with your local method to handle it.
                
                Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                      your backtest.game.php file.
            
            */
            setupNotifications: function()
            {
                console.log( 'notifications subscriptions setup' );
    
                dojo.subscribe( 'move', this, "notif_move" );
                dojo.subscribe( 'movefromreserve', this, "notif_movefromreserve" );
                dojo.subscribe( 'score', this, "notif_score" );
                dojo.subscribe( 'forfeit', this, "notif_forfeit" );
                
                // TODO: here, associate your game notifications with local methods
                
                // Example 1: standard notification handling
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
                
                // Example 2: standard notification handling + tell the user interface to wait
                //            during 3 seconds after calling the method in order to let the players
                //            see what is happening in the game.
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
                // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
                // 
            },  
            
            // TODO: from this point and below, you can write your game notifications handling methods
            
            /*
            Example:
            
            notif_cardPlayed: function( notif )
            {
                console.log( 'notif_cardPlayed' );
                console.log( notif );
                
                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
                
                // TODO: play the card in the user interface.
            },    
            
            */
            
            notif_move: function( notif )
            {
               // pour rappel, définis dans la fonction function actSelect du game.php notif.args.mobile et notif.args.parent sont les ID du token qui bouge et du square de destination
            
               
            if(notif.args.movetypeafter == 5 )
               {
                dojo.query("#"+notif.args.mobile).removeClass("tokentype_"+notif.args.movetype);
                dojo.query("#"+notif.args.mobile).addClass("tokentype_5");
               }
               
            var nouvelid = notif.args.parent.split("_"); // nouvelid[]=[square][x de destination][y de destination]
                           
            var divElement = document.getElementById(notif.args.mobile);  // divElement = le div complet du div qui comporte l'id du token qui bouge
                      
            var divToRemove = document.getElementById('token_'+nouvelid[1]+'_'+nouvelid[2]); // divToRemove = si yen a un, le complet div qui comporte l'id du token present dans le square de destination
                
            divElement.id = 'token_'+nouvelid[1]+'_'+nouvelid[2]; // on change l'id du div du token qui vient de bouger avec les mêmes coord que le square de destination
            notif.args.mobile = divElement.id; // et enfin on reinjecte le nouvel id du token qui bouge dans notif.args.mobile
            
    
            if (divToRemove) // si y a un token dans le square de destination, on l'envoie dans la réserve du joueur actif
            {
                if(notif.args.typekillafter == 4)
                {
                    divToRemove.remove();
            
                }
                
                else
                {
                var x = parseInt(notif.args.nbrelignereserve, 10); // transformer un string en un entier sur la base 10
                divToRemove.id = 'tokenreserve'+notif.args.reserve+'_'+x;
                var destinationDiv = document.getElementById('squarereserve'+notif.args.reserve+'_'+x);
                
                if (notif.args.reserve == 1 )
                {
                    dojo.query("#tokenreserve1_"+x).removeClass("tokencolor_0000ff");
                    dojo.query("#tokenreserve1_"+x).addClass("tokencolor_008c00");
                    if (notif.args.typekill == 5 ){
                        dojo.query("#tokenreserve1_"+x).removeClass("tokentype_5");
                        dojo.query("#tokenreserve1_"+x).addClass("tokentype_1");
                    }   
    
                }
                
                if (notif.args.reserve == 2 )
                {
                    dojo.query("#tokenreserve2_"+x).removeClass("tokencolor_008c00");
                    dojo.query("#tokenreserve2_"+x).addClass("tokencolor_0000ff");
                    if (notif.args.typekill == 5 ){
                        dojo.query("#tokenreserve2_"+x).removeClass("tokentype_5");
                        dojo.query("#tokenreserve2_"+x).addClass("tokentype_1");
                    }
    
                }
                
                this.attachToNewParentNoDestroy( divToRemove.id, destinationDiv.id );
                element = document.getElementById(divToRemove.id);
                void element.offsetWidth;
                dojo.style(divToRemove.id, {
                               "left":"0px",
                                "top": "0px",
                                "transition":"0.5s"
                });
                               
                setTimeout(() => {
                dojo.style(divToRemove.id, {
                               "position":"",
                               "left":"",
                                "top": "",
                                "bottom": "",
                                "right": "" ,
                                "transition":""
                });
                }, "600");

                   
                }
                
            }
    
            // le mouvement de la piece sur le plateau
            this.attachToNewParentNoDestroy( notif.args.mobile, notif.args.parent );
            element = document.getElementById(notif.args.mobile);
            void element.offsetWidth;
            dojo.style(notif.args.mobile, {
                               "left":"0px",
                                "top": "0px",
                                "transition":"0.5s"
            });
                               
            setTimeout(() => {
            dojo.style(notif.args.mobile, {
                               "position":"",
                               "left":"",
                                "top": "",
                                "bottom": "",
                                "right": "" ,
                                "transition":""
            });
            }, "600");
    
            
            },
            
         
    
            notif_movefromreserve: function( notif )    // mouvement à partir de la reserve vers le plateau
            {
               
    
            var divElement = document.getElementById(notif.args.mobile);  // divElement = le div complet du div qui comporte l'id du token qui bouge
            var nouvelid = notif.args.parent.split("_"); // nouvelid[]=[square][x de destination][y de destination]
            divElement.id = 'token_'+nouvelid[1]+'_'+nouvelid[2]; // on change l'id du div du token qui vient de bouger avec les mêmes coord que le square de destination
            notif.args.mobile = divElement.id; // et enfin on reinjecte le nouvel id du token qui bouge dans notif.args.mobile
    
            this.attachToNewParentNoDestroy( notif.args.mobile, notif.args.parent );
            element = document.getElementById(notif.args.mobile);
            void element.offsetWidth;
            dojo.style(notif.args.mobile, {
                               "left":"0px",
                                "top": "0px",
                                "transition":"0.5s",
                                "z-index": "100"
            });
                               
            setTimeout(() => {
            dojo.style(notif.args.mobile, {
                               "position":"",
                               "left":"",
                                "top": "",
                                "bottom": "",
                                "right": "" ,
                                "transition":"",
                                "z-index": ""
                                
            });
            }, "600");
    
            // decalage de la reserve
            var x = parseInt(notif.args.position, 10);
            var y = parseInt(notif.args.nombre, 10);
    
            if (x < y)
            {
            
            
                if ( notif.args.tokenreserve == 'tokenreserve1' )
                {
                    for ( var i = x + 1; i <= y; i++ )
                    {
                       
                    var token = document.getElementById('tokenreserve1_'+i);
                    token.id = 'tokenreserve1_'+(i-1);
                    notif.args.mobile = token.id;
                    notif.args.parent = 'squarereserve1_'+(i-1);
                
    
                    this.attachToNewParentNoDestroy( notif.args.mobile, notif.args.parent );
                    element = document.getElementById(notif.args.mobile);
                    void element.offsetWidth;
                    dojo.style(notif.args.mobile, {
                               "left":"0px",
                                "top": "0px",
                                "transition":"0.5s"
                    });
                               
                    setTimeout(() => {
                    dojo.style(notif.args.mobile, {
                               "position":"",
                               "left":"",
                                "top": "",
                                "bottom": "",
                                "right": "" ,
                                "transition":""
                    });
                    }, "600");
                    
                    }
                    
                }
    
                if ( notif.args.tokenreserve == 'tokenreserve2' )
                {
    
                    for ( var i = x + 1; i <= y; i++ )
                    {
                       
                    var token = document.getElementById('tokenreserve2_'+i);
                    token.id = 'tokenreserve2_'+(i-1);
                    notif.args.mobile = token.id;
                    notif.args.parent = 'squarereserve2_'+(i-1);
                
    
                    this.attachToNewParentNoDestroy( notif.args.mobile, notif.args.parent );
                    element = document.getElementById(notif.args.mobile);
                    void element.offsetWidth;
                    dojo.style(notif.args.mobile, {
                               "left":"0px",
                                "top": "0px",
                                "transition":"0.5s"
                    });
                               
                    setTimeout(() => {
                    dojo.style(notif.args.mobile, {
                               "position":"",
                               "left":"",
                                "top": "",
                                "bottom": "",
                                "right": "" ,
                                "transition":""
                    });
                    }, "600");
                    
                    }
                
    
                }
    
            
    
            }
    
    
    
    
    
            },
    
    
    
            notif_score: function( notif )
            {
                for( var player_id in notif.args.score )
                {
                    var newScore = notif.args.score[ player_id ];
                    this.scoreCtrl[ player_id ].toValue( newScore );
                }
            },

            notif_forfeit: function( notif )
            {
                for( var player_id in notif.args.score )
                {
                    var newScore = notif.args.score[ player_id ];
                    this.scoreCtrl[ player_id ].toValue( newScore );
                }
            }
    
        
    
    
            
       });             
    });
    