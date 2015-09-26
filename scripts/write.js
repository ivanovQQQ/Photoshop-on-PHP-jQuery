

var fontImg = $('#textEdit img[data-font]'),
    sizeInp = $('#sizeInp'),
    sizeSub = $('#sizeSub'),
    colorSel= $('.colors img'),
    colorInp= $('#colorInp'),
    colorIn = $('[name=color]'),
    fontIn  = $('[name=font]'),
    sizeIn  = $('[name=size]'),
    textSub = $('#submitTextEdit'),
    
    alignSel= $('.align'),
    alignIn = $('#align'),
    
    color, fontFamily, fontSize, align;
   

 
textSub.click(function(e){
    preventdefault(e);
    textSub.prop('disabled', true);
    var src = pathinp.val(),
        t   = text.html(),
        x   = text.css('left'),
        y   = text.css('top'),
        c   = colorIn.val(),
        f   = fontIn.val(),
        s   = sizeIn.val();
        
    $.ajax({
        type : 'post',
        url  : 'workerText.php',
        //processData: false,
        data : {src:src, text:t, x:x, y:y, font:f, color:c, size:s},
        success : function(response){
            console.log(response);
            if(/[а-я]/.test(response)
            || !response){
                thumbnail.attr("src", 'img/gon.jpg');
            }else{                    
                 thumbnail.attr("src", response);
                         pathinp.val(response);
                         thumbnail.on('load', function(){
                         imgW = thumbnail.width();
                         imgH = thumbnail.height();
                         //console.log(imgW, imgH);
                         write.css({'width':imgW, 'height':imgH});
                         text.hide().html('');
                         });
                     }
            textSub.prop('disabled', false);
        }
    });
});

alignSel.click(function(){
    align = $(this).attr('data-a');
    text.css({
        textAlign : align
    });
    
});
   
body.on('mouseup', writed, function(e){ 
        if(u.b){
          e = e || window.event;
          if(e.which == 1){
           if (!text.is(e.target) // если клик был не по нашему блоку
		    && text.has(e.target).length === 0 ) { // и не по его дочерним элементам
			var offset = write.offset();
                text.show().css({
                    left: e.pageX - offset.left, 
                    top: e.pageY - offset.top,
                    maxWidth : imgW - 22
                }).focus();
                //console.log((e.pageX - offset.left) + 'x' + (e.pageY - offset.top));
                $('[name=x]').val(e.pageX - offset.left);
                $('[name=y]').val(parseInt(e.pageY - offset.top));
		  }   
         }   
        }
});

/*
colorSel.click(function(){
    color = $(this).attr('data-color');
    text.css({
             color : color
    });
    colorIn.val(color);
});

colorInp.on('keydown', function(e){
    if(e.which == 13){
        preventdefault(e);
        color = $(this).val();
        if(/^#[a-z\d]{6}$/.test(color)){
            
           	text.css({
                color : color
            });
            colorIn.val(color);
        }
    }
});
*/

fontImg.click(function(){
    fontFamily = $(this).attr('data-font');
    text.css({
        fontFamily : fontFamily
    });
    fontIn.val(fontFamily);
});

sizeInp.on('keydown', function(e){
    if(e.which == 13){
        preventdefault(e);
        if(fontSize = $(this).val()){
            text.css({
                fontSize : fontSize + 'px'
            });
            sizeIn.val(fontSize);
        }
    }
});

sizeSub.on('click', function(){
    if(/\d+/.test(sizeInp.val())){
        text.css({
            fontSize : sizeInp.val() + 'px'
        });
        sizeIn.val(sizeInp.val());
    }
    
});

body.on('keydown', text, function(e){
    if(e.which == 27){
        text.html('');
    }
});

/*// установим обработчик события mousedown, элементу с идентификатором foo
$(window).mousedown(function(eventObject){
  alert('Вы нажали на кнопку мыши, над элементом "foo". Код нажатой клавиши - ' + eventObject.which);
});*/
