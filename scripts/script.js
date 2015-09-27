    
    var body      = $('body'),
        thumbnail = $("#thumbnail"),
        pathinp   = $('#source'),
        
        write     = $('#write'),
        writed    =  '#write',
        text      = $('#text'),
    
        button1   = $('#buttupload'),
        hideDiv   = $('#hide'),
        
        imgW, imgH;
    
    
thumbnail.on('load', function(){
    imgW = thumbnail.width();
    imgH = thumbnail.height();
    //console.log(imgW, imgH);
    write.css({'width':imgW, 'height':imgH});
});


$('#my_form').on('submit', function(e){
    
    preventdefault(e); // предотвращение отправки формы по умолчанию
    
    button1.prop("disabled", true); // блокировка кнопки
    
    var $that = $(this),
    formData = new FormData($that.get(0)); // создаем новый экземпляр объекта и передаем ему нашу форму
		
	$.ajax({
		url: $that.attr('action'),
		type: $that.attr('method'),
		contentType: false, // важно - убираем форматирование данных по умолчанию
		processData: false, // важно - убираем преобразование строк по умолчанию
        dataType : 'json',
		data: formData,
		success: function(response){
            //console.log(response);
                     // при неверных данных делаю exit('()') в worker.php
                     if(response.save == null){ // в файле-обработчике отключил вывод ошибок из-за недоступности brightnessContrastImage
                         thumbnail.attr("src", 'img/gon.jpg');
                     }else{                    
                         thumbnail.attr("src", response.save);
                         pathinp.val(response.save);
                         //thumbnail.imgAreaSelect({remove:true});
                         thumbnail.on('load', function(){
                            imgW = thumbnail.width();
                            imgH = thumbnail.height();
                            var str = imgW +'x'+ imgH;
                            if(response.delay || response.countframes){
                                response.delay = response.delay || 10;
                                str += '<br>Скорость гифки '+ response.delay + '<br>Количество кадров ' + response.countframes;
                            }
                            $('#getimage').html(str);
                            write.css({'width':imgW, 'height':imgH});
                         });
                     }
             button1.prop("disabled", false);
         }
    });
});  
    
    var areaOn = $('#area'),
        u = {
            a : 0,
            b : 1
        };
    areaOn.on('click', function(){
        if(u.a === 0){
            thumbnail.imgAreaSelect({handles: true, keys: { arrows: 15, ctrl: 5, shift: 'resize' }, onSelectChange: preview});
            areaOn.text('Выключить ImageArea');
            text.hide();
            u.a++;
            u.b--;
            return u;
        }else{
            thumbnail.imgAreaSelect({remove:true}); //For hiding the imagearea
            areaOn.text('Включить ImageArea');
            u.a--;
            u.b++;
            return u;
        }
    });
    
    $('#arearemove').on('click', function(){
    
    });
    
    function preview (img, selection) {

			$("#x1").val(selection.x1);
			$("#y1").val(selection.y1);
			$("#x2").val(selection.x2);
			$("#y2").val(selection.y2);
			$("#w").val(selection.width);
			$("#h").val(selection.height);
			
		}
        
        $("#butSave").click(function () {
            
            hideDiv.show();
            
            var image = $('#source').val();
			var x1 = $("#x1").val();
			var y1 = $("#y1").val();
			var x2 = $("#x2").val();
			var y2 = $("#y2").val();
			var w = $("#w").val();
		    var h = $("#h").val();
			$.ajax ({
				url: "worker_crop.php",
				type: "POST",
				data: {image: image, x1: x1, y1: y1, w: w, h: h},
				success: function (response) {
                   //console.log(response)
                   $('#response').html(response);
                   hideDiv.hide();
                }
	});
    });
    
    

function preventdefault(e){
    e = e || window.event;
    if(e.preventDefault) e.preventDefault();
    else e.returnValue  = false;
    
}

function equalHeightWidth (a, b, c){
	var a = $(a);
	var b = $(b);
if(a.height() > b.height())	{
	b.height(a.height());
}
else{
	a.height(b.height());
}
if(c === true){
	if(a.width()>b.width()){
		b.width(a.width());
	}
	else{
		a.width(b.width());
	}
}
}
