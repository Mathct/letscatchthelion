{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- LetsCatchTheLion implementation : © <Mathieu Chatrain> <mathieu.chatrain@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    letscatchthelion_letscatchthelion.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->

<div id="global">
    
        <div id="plateau">
            <div id="board">
                <!-- BEGIN square -->
                <div id="square_{X}_{Y}" class="square" style="left: {LEFT}px; top: {TOP}px;">
                </div>
                <!-- END square -->
            </div>
        </div>
    <div id="reserve1">
        <!-- BEGIN squarereserve1 -->
        <div id="squarereserve1_{X}" class="squarereserve1" style="left: {LEFT}px; top: {TOP}px;">
        </div>
        <!-- END squarereserve1 -->
    </div>
    <div id="reserve2">
        <!-- BEGIN squarereserve2 -->
        <div id="squarereserve2_{X}" class="squarereserve2" style="left: {LEFT}px; top: {TOP}px;">
        </div>
        <!-- END squarereserve2 -->
    </div>
</div>

<script type="text/javascript">

var jstpl_token='<div class="token tokencolor_${color} tokentype_${type}" id="token_${x_y}"></div>';
var jstpl_tokenreserve1='<div class="token tokencolor_${color} tokentype_${type}" id="tokenreserve1_${x}"></div>';
var jstpl_tokenreserve2='<div class="token tokencolor_${color} tokentype_${type}" id="tokenreserve2_${x}"></div>';

</script> 

{OVERALL_GAME_FOOTER}
