function Suggest(){
    this.timer = null;
    this.xhr = new XMLHttpRequest();

    this.getPeople = function(){
        if(this.timer){
            clearTimeout(user.timer);
        }
    //Call to go to the server in 1 second
        this.timer = setTimeout(user.fetchPeople,1000);
    }

    this.getUsername = function(){
        return document.getElementById("currUsername").innerHTML;
    }

    this.getSearchValue = function(){
        return document.getElementById("searchbox").value;
    }

    this.getdivcontainer = function(){
        return document.getElementById("display-result");
    }

    this.sendFollowRequest = function(clicked){
        user.xhr.onreadystatechange = function(){
            if(this.readyState==4 && this.status==200){
                console.log(JSON.parse(this.responseText));
                user.fetchPeople();
            }
        };
        user.xhr.open("GET","http://localhost:3000/users/sendfollowrequest/" + user.getUsername() + "/" + clicked.attributes["name"].value,true);
        user.xhr.send();
    }
    
    this.fetchPeople = function(){
        if(user.getSearchValue() == ""){
            user.getdivcontainer().innerHTML = "";
            user.getdivcontainer().style.display = "none";
            return;
        }else{
           /* if(localStorage[user.getSearchValue()]){
                console.log(localStorage[user.getSearchValue()]);
                user.showPeople(JSON.parse(localStorage[user.getSearchValue()]));
            }else{*/
                user.xhr.onreadystatechange = user.populatePeople;
                user.xhr.open("GET","http://localhost:3000/users/search/" + user.getUsername() + "/" + user.getSearchValue(),true);
                user.xhr.send();
            //}
        }
    }

    this.populatePeople = function(){
        if(this.readyState==4 && this.status==200){
            res = JSON.parse(this.responseText);
            console.log(res);
            if(res.success == true){
                users = res.data;
                localStorage[user.getSearchValue()] = this.responseText;
                user.showPeople(users);
            }else{
                user.getdivcontainer().innerHTML = "";
                user.getdivcontainer().style.display = "none";
            }
        }
    }

    this.showPeople = function(userlist){
       user.getdivcontainer().innerHTML = "";

        for(i=0; i< userlist.length; i++){
            displayRes = "<tr class='result-box'><td style='width:7%;'><img src='users/" + userlist[i].username + "/" + userlist[i].profilepic + "' style='width:90%; border-radius:50px'></td>";
            displayRes += "<td style='width:84%;padding-left:15px;'><a style='font-size:150%;font-weight:600;color:black;'href=''>" + userlist[i].name + "</a><p style='color:grey'>" + userlist[i].username + "</p></td>";
            if(userlist[i].relationship == "FRIENDSHIP")
                displayRes += "<td style='width:20%;' ><button id='add-btn-" + userlist[i].username + "' name='" + userlist[i].username + "' class='add-btn'>Following</button></td></tr>";
            else if(userlist[i].relationship == "HAS_REQUESTED")
                displayRes += "<td style='width:20%;' ><button id='add-btn-" + userlist[i].username + "' name='" + userlist[i].username + "' class='add-btn'>Requested</button></td></tr>";
            else
                displayRes += "<td style='width:20%;' ><button id='add-btn-" + userlist[i].username + "' name='" + userlist[i].username + "' onclick='user.sendFollowRequest(this)' class='add-btn'><img src='img/accept.png' width='20'/> Follow</button></td></tr>";
            console.log(userlist[i].relationship);
            user.getdivcontainer().innerHTML += displayRes;
        }
        user.getdivcontainer().style.display = "block";
    }

    this.setUser = function(event){
        user.getSearchValue() = event.target.innerHTML;
        user.getdivcontainer().innerHTML = "";
        user.getdivcontainer().style.display = "none";
    }
}

user = new Suggest();