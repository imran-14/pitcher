function Notification(){
    this.xhr = new XMLHttpRequest();

    this.getUsername = function(){
        return document.getElementById("currUsername").innerHTML;
    }
    this.sendAcceptRequest = function(clicked){
        notification.xhr.onreadystatechange = function(){
            if(this.readyState==4 && this.status==200){
                console.log(JSON.parse(this.responseText));
            }
        };
        notification.xhr.open("GET","http://localhost:3000/users/addfollower/" + notification.getUsername() + "/" + clicked.attributes["name"].value,true);
        notification.xhr.send();
    }
}

notification = new Notification();
es = new EventSource("http://localhost:3000/users/notifications/" + notification.getUsername());
notifyDiv = document.getElementById("notification");
es.addEventListener("notification", function(e) {
    notif = "";
    data = JSON.parse(e.data);
    for (var key in data) {
        notif += "<tr><td style='width:80%;'><a id='add-btn-" + data[key] + "' style='color:#e5e5e5' href=''>" + data[key] + "</a></td>";
        notif += "<td style='width:20%' ><button style='border:none; id='" + data[key] + "' name='" + data[key] + "' onclick='notification.sendAcceptRequest(this)' color:white; background-color:#4c4c4c; cursor:pointer; outline:none;'><img src='img/accept.png' style='position:relative;width:80%'></button></td></tr>"; 
    }
    notifyDiv.innerHTML = notif;
});