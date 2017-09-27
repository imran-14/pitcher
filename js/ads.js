function Ads() {
	this.xhr= new XMLHttpRequest();
	
	this.refreshData= function(){
		obj.xhr.onreadystatechange = obj.showData;
		
		obj.xhr.open("GET", "http://localhost/Pitcher/getfeed.php", true);
		
		obj.xhr.send();
		 
		setTimeout(obj.refreshData,5000);

	};

	this.showData = function(){
		if(obj.xhr.readyState==4 && obj.xhr.status==200){
			doc=obj.xhr.responseXML;
			root=doc.documentElement;
			items=root.getElementsByTagName("item");

            ads = document.getElementById("ads");
			ads.innerHTML="";
			div=document.createElement("div");
			for(i in items){
				if(Number.isInteger(parseInt(i)) ){
				a=document.createElement("a");
				a.innerHTML=items[i].getElementsByTagName("description")[0].firstChild.nodeValue;
				a.href=items[i].getElementsByTagName("link")[0].firstChild.nodeValue;
				div.appendChild(a);
				}
			}
			ads.appendChild(div);
			
		}
	};
}

obj = new Ads();
obj.refreshData();