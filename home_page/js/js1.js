window.onload=function(){
	var img1 = document.getElementById('img-1');
	var nameone = document.getElementById('name-one');
	var img2 = document.getElementById('img-2');
	var introduceone = document.getElementById('introduce-one');
	var img3 = document.getElementById('img-3');
	var ouraimone = document.getElementById('our-aim-one');*/
	var timer = null;
	img1.onmouseover=function(){
			clearTimeout(timer);
			nameone.style.display='block';
		};
	img1.onmouseout=function(){
			timer=setTimeout(function(){nameone.style.display='none';},500);
		};
	nameone.onmouseover=function(){

			clearTimeout(timer);
		};
		nameone.onmouseout=function(){
			timer=setTimeout(function(){nameone.style.display='none';},500);
		};
	img2.onmouseover=function(){
			clearTimeout(timer);
			introduceone.style.display='block';
		};
	img2.onmouseout=function(){
			timer=setTimeout(function(){introduceone.style.display='none';},500);
		};
	introduceone.onmouseover=function(){

			clearTimeout(timer);
		};
	introduceone.onmouseout=function(){
			timer=setTimeout(function(){introduceone.style.display='none';},500);
		};
	img3.onmouseover=function(){
			clearTimeout(timer);
			ouraimone.style.display='block';
		};
	img3.onmouseout=function(){
			timer=setTimeout(function(){ouraimone.style.display='none';},500);
		};
	ouraimone.onmouseover=function(){

			clearTimeout(timer);
		};
	ouraimone.onmouseout=function(){
			timer=setTimeout(function(){ouraimone.style.display='none';},500);
		};



}