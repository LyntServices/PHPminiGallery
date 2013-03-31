if (effects>0){
$(document).ready(function() { 
$("#galerie a").fadeTo(1, 0.3);
$("#galerie a").one("mouseenter", s_up);
      });  
}      
      
function s_up(){
$(this).stop();
$(this).one("mouseleave", s_down);
$(this).fadeTo(300, 1);
}    

function s_down(){
$(this).stop();
$(this).one("mouseenter", s_up);
$(this).fadeTo(850, 0.3);
}      

