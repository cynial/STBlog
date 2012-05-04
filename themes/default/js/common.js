//查找网页内宽度太大的图片进行缩放以及PNG纠正
function ReImgSize()
{
	for (i=0;i<document.images.length;i++)
	{
		//IE
		if(document.all)
		{
			if (document.images[i].width>550)
			 {
			   document.images[i].width="550"
			   try
			   {
			       document.images[i].outerHTML='<a href="'+document.images[i].src+'" target="_blank" title="在新窗口打开图片">'+document.images[i].outerHTML+'</a>'
			   }
				catch(e){}
			}
		}
		//safari && opera && firefox
		else
		{
			if (document.images[i].width>550) 
			{
			  document.images[i].width="500"
			  document.images[i].title="在新窗口打开图片"
			  document.images[i].style.cursor="pointer"
			  document.images[i].onclick=function(e){window.open(this.src)}
			}
		}
	}
}