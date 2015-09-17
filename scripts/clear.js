

window.onbeforeunload = function(){
     $.ajax({
         type: 'POST',
         url: 'clear.php',
         success : function(response){
             console.log(response)
         }
     });       
}