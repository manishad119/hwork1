var map, marker;
function checkLogin(){
    var uid = window.localStorage.getItem("uid");
    var phash = window.localStorage.getItem("phash");
    if(!uid){
        return false;
    }
    if(!phash){
        return false;
    }
}
function getLoginPageUrl(){
    return "login.html";
}
function login(uid, phash){
    if(!uid){return false;}
    if(!phash){return false;}
    window.localStorage.setItem("uid", uid);
    window.localStorage.setItem("phash", phash);
    return true;
}
function getUID(){
    return window.localStorage.getItem("uid");
}
function getLogin(){
    var uid = window.localStorage.getItem("uid");
    var phash = window.localStorage.getItem("phash");
    if(!uid || !phash){
        return {};
    }
    var data = {"uid": uid, "phash": phash};
    return data;
}
function getAPIBase(){
    return "http://localhost/pjmgmt/api";
}
function getMobileBase(){
    return "http://localhost/mobile";
}
function getHost(val){
    var base = getAPIBase();
    var urls = {
        "login": "/login/index.php", //uname, password
        "borrow": "/borrow/index.php",
        "comment": "/comment/index.php",
        "item": "/item/index.php",
        "items": "/items/index.php",
        "lend": "/lend/index.php",
        "publish": "/publish/index.php",
        "signup": "/register/index.php",
        "return": "/return/index.php",
        "users": "/users/index.php",
        "user": "/user/index.php",
        "search-user": "/search/users/index.php",
        "search-item": "/search/items/index.php",
        "notification": "/notification/index.php"
    }
    if(urls[val]){
        return base+urls[val];
    }
    return null;
}
function postData(type, data, loginRequired){
    var loginData = getLogin();
    if((!loginData["uid"] && !loginData["phash"]) && loginRequired == true){
        return {"success": 0, "msgid": 0, "msg": "Not Logged In"};
    }
    if(loginRequired == true){
        if(loginData["uid"]){data.append("uid", loginData["uid"]);}
        if(loginData["phash"]){data.append("hash", loginData["phash"]);}
    }
    else if(loginData["uid"] && loginData["phash"]){
        if(loginData["uid"]){data.append("uid", loginData["uid"]);}
        if(loginData["phash"]){data.append("hash", loginData["phash"]);}
    }
    $.ajax({
        url: getHost(type),
        method: "POST",
        data: data,
        contentType: false,
        processData: false,
    }).done(manageAjax.bind(null, type));
    return "Done";
}
function commentToAItem(){
    var t = $(".comment-area").val();
    var formData = new FormData();
    formData.append("comment", t);
    var iid = $(".main-item-footer").attr("data-iid");
    formData.append("itemId", iid);

    postData("comment", formData, true);
}
function manageAjax(type, data){
    if(typeof(data) == "string"){
        data = JSON.parse(data);
    }
    if(type == "signup"){signUpD(data);}
    if(type == "login"){loginD(data);}
    if(type == "publish"){loadPageItemAfterPublish(data);}
    if(type == "item"){loadPage("itempage", data);}
    if(type == "user"){loadPage("userpage", data);}
    if(type == "items"){data["type"] = "items";loadPage("mainpage", data);}
    if(type == "users"){data["type"] = "users";loadPage("mainpage", data);}
    if(type == "search-item"){data["type"] = "items";loadPage("searchpage", data);}
    if(type == "search-user"){data["type"] = "users";loadPage("searchpage", data);}
    if(type == "lend"){
        if(!checkSuccess(data)){return false;}
        hideFooter();
        openItemById($(".main-item-footer").attr("data-iid"));
    }
    if(type == "return"){
        if(!checkSuccess(data)){return false;}
        hideFooter();
        openItemById($(".main-item-footer").attr("data-iid"));
    }
    if(type == "borrow"){
        if(!checkSuccess(data)){return false;}
        hideFooter();
        openItemById($(".main-item-footer").attr("data-iid"));
    }
    if(type == "comment"){
        if(!checkSuccess(data)){return false;}
        openItemById($(".main-item-footer").attr("data-iid"));
        var t = $(".comment-area").val("");
    }
    if(type == "notification"){
        if(!checkSuccess(data)){return false;}
        console.log(data);
        manageNotification(data);
    }
}
function hideFooter(){
    $(".footer-icon").addClass("hide");
}
function loadPageItemAfterPublish(data){
    if(!checkSuccess(data)){return false;}
    var item = data["data"];
    if(item){
        if(item["itemId"]){
            openItemById(item["itemId"]);
            $(".publish-input").val("");
            $(".map-btn").html("Set Location (Not Set)");
        }
    }
    return false;
}
function loginD(data){
    if(!checkSuccess(data)){return false;}
    if(data["cookies"]){
        data = data["cookies"];
        if(login(data["uid"], data["hash"])){
            window.location = window.getMobileBase()+"/index.html";
        }
    }
}
function signUpD(data){
    if(!checkSuccess(data)){return false;}
    console.log(data);
    if(data["cookies"]){
        data = data["cookies"];
        if(login(data["uid"], data["hash"])){
            window.location = window.getMobileBase()+"/index.html";
        }
    }
}
function nearByItems(){
    var loc = getCurrentLocation();
    var formData = new FormData();
    formData.append("longitude", loc["longitude"]);
    formData.append("latitude", loc["latitude"]);
    postData("items", formData, true);
}
function nearByPeoples(){
    var loc = getCurrentLocation();
    var formData = new FormData();
    formData.append("longitude", loc["longitude"]);
    formData.append("latitude", loc["latitude"]);
    postData("users", formData, true);
}
function checkSuccess(data){
    if(!data["success"]){
        return false;
    }
    return true;
}

function isItemSearchOn(){
    if($("#search-item").attr("checked")){
        return true;
    }else{
        return false;
    }
}
function searchPeople(){
    $(".searchpage-people-list").html("");
    $(".searchpage-item-list").html("<div> <br></div>");
    $("#search-item").removeAttr("checked");
    $("#search-people").attr("checked", "checked");
    var val = getSearchString();
    if(val){searchData(false, val);}
}
function searchItem(){
    $(".searchpage-item-list").html("");
    $(".searchpage-people-list").html("<div> <br></div>");
    $("#search-people").removeAttr("checked");
    $("#search-item").attr("checked", "checked");
    var val = getSearchString();
    if(val){searchData(true, val);}
}
function getSearchString(){
    if($(".search-input").val()){
        return $(".search-input").val();
    }
    return "";
}
function searchData(isItem, val){
    var formData = new FormData();
    formData.append("q", val);
    if(isItem){
        postData("search-item", formData, true);
    }else{
        postData("search-user", formData, true);
    }
}
function loadPage(val, data){
    $(".search-input").css("display", "none");
    var success = false;
    if(val == "mainpage"){if(loadMainPage(data)){success = true;hideFooter();}}
    else if(val == "publishpage"){if(loadPublishPage(data)){success = true;hideFooter();}}
    else if(val == "userpage"){if(loadUserPage(data)){success = true;hideFooter();}}
    else if(val == "mappage"){if(loadMapPage(data)){success = true;hideFooter();}}
    else if(val == "itempage"){if(loadItemPage(data)){success = true;}}
    else if(val == "searchpage"){$(".search-input").css("display", "");if(loadSearchPage(data)){success = true;hideFooter();}}
    if(success){
        showThisPage(val);
    }
}
function loadSearchPage(data){
    $(".nav-on").removeClass("nav-on");
    $(".search-button-btn").addClass("nav-on");
    var returnType = false;
    if(data["type"] == "items"){
        returnType = loadMainItemsPage("search", data);
    }else if(data["type"] == "users"){
        returnType = loadMainUsersPage("search", data);
    }
    if(!returnType){return false;}
    return true;
}
function loadMainPage(data){ // Load Main Page
    $(".nav-on").removeClass("nav-on");
    $(".back-button").addClass("nav-on");
    var returnType = false;
    if(data["type"] == "items"){
        returnType = loadMainItemsPage("main", data);
    }else if(data["type"] == "users"){
        returnType = loadMainUsersPage("main", data);
    }
    if(!returnType){return false;}
    return true;
}
function loadMainItemsPage(page, data){ //Items of main page
    //mainpage-item-list
    if(!checkSuccess(data)){return false;}
    try{
        var items = data.data;
        if(page == "main"){
            $(".mainpage-item-list").html(getItemList(-1, items));
            $(".people").removeAttr("checked");
            $(".item").attr("checked", "checked");
        }else if(page == "search"){
            $(".searchpage-item-list").html(getItemList(-1, items));
            $(".search-people").removeAttr("checked");
            $(".search-item").attr("checked", "checked");
        }else{
            return false;
        }
    }catch(e){
        return false;
    }
    return true;
}
function loadMainUsersPage(page, data){ // Users of main page
    if(!checkSuccess(data)){return false;}
    try{
        var users = data.data;
        if(page == "main") {
            $(".mainpage-people-list").html(getPeopleList(users));
            $(".item").removeAttr("checked");
            $(".people").attr("checked", "checked");
        }else if(page == "search"){
            $(".searchpage-people-list").html(getPeopleList(users));
            $(".search-item").removeAttr("checked");
            $(".search-people").attr("checked", "checked");

        }
    }catch(e){
        return false;
    }
    return true;
}
function loadPublishPage(data){ //Load Publish Page
    $(".nav-on").removeClass("nav-on");
    $(".publish-item").addClass("nav-on");
    return true;
}
function loadMapPage(data){ //Load Map Page
    return true;
}
function openItemById(id){
    var formData = new FormData();
    formData.append("itemId", id);
    postData("item", formData, true);
}
function openUserById(id){
    var formData = new FormData();
    formData.append("userId", id);
    postData("user", formData, true);
}
function loadUserPage(data){ //Load User Page
    if(!checkSuccess(data)){return false;}
    try{
        var idata = data.data;
        var user = idata["user"];
        var items = idata["items"];

        $(".profile-name").html(user["fname"]);
        $(".profile-email").html(user["email"]);
        $(".profile-contact").html(user["contact"]);
        $(".profile-uname").html(user["uname"]);
        if(user["gender"] == 1){
            $(".profile-gender").html("");
            $(".profile-gender").attr("class", "profile-gender fa fa-male");
        }else if(user["gender"] == 2){
            $(".profile-gender").html("");
            $(".profile-gender").attr("class", "profile-gender fa fa-female");
        }else{
            $(".profile-gender").attr("class", "profile-gender");
            $(".profile-gender").html("others");
        }
        $(".user-profile").css("background-image", "url('"+user["pic"]+"')");
        $(".userpage-item-list").html(getItemList(user["uid"], items));

        $(".nav-on").removeClass("nav-on");
        if(getUID() == user["uid"]){
            $(".profile-page").addClass("nav-on");
        }
        $("#userpage-iid").val(user["uid"]);
        setPageTitle(user["fname"]);
        return true;
    }
    catch(e){
        return false;
    }
}

function getPeopleList(users){
    var val = "";
    for (var key in users) {
        if (users.hasOwnProperty(key)) {
            var userVal = users[key];
            var item = '<div class="items" onclick="openUserById('+userVal["uid"]+')"><div class="item-image-icon"><img class="item-img" src="'+userVal["pic"]+'" /></div><div class="item-detail"><div class="item-name">'+userVal["fname"]+'</div><div class="item-status">Gender: ';
            if(userVal["gender"] == 1){
                item+='<i class="mainpage-people-gender fa fa-male"></i>';
            }else if(userVal["gender"] == 2){
                item += '<i class="mainpage-people-gender fa fa-female"></i>';
            }else{
                item+='<i class="mainpage-people-gender">Others</i>';
            }
            item+='</div></div></div>';
            val+=item;
        }
    }
    return val;
}
function getItemList(userId, items){
    var val = "";
    for (var key in items) {
        if (items.hasOwnProperty(key)) {
            var itemVal = items[key];
            if(userId == -1){userId = itemVal["uid"];}
            var item = '<div class="items" onclick="openItemById('+itemVal["iid"]+')"><div class="item-image-icon"><img class="item-img" src="'+itemVal["itemImageSrc"]+'" /></div><div class="item-detail"><div class="item-name">'+itemVal["itemName"]+'</div><div class="item-status">';
            if(userId == getUID()){
                if(itemVal["status"] == 0){
                    item+= 'Returned: <i class="fa fa-check';
                }else if(itemVal["status"] == 1){
                    item+= 'Borrowed: <i class="fa fa-check';
                }else{
                    item+= 'Lended: <i class="fa fa-check';
                }
            }else{
                item+='Available: <i class="fa fa-';
                if(itemVal["status"] == 0){
                    item += "check";
                }else{
                    item += "close";
                }
            }
            item+='"></i></div></div></div>';
            val += item;
        }
    }
    return val;
}
function loadItemPage(data){ //Load Item Page
    if(!checkSuccess(data)){return false;}
    try{
        var idata = data.data;
        $(".item-name").html(idata["itemName"]);
        $(".item-money").html(idata["money"]);
        $(".item-period").html(idata["period"]);
        $(".item-longitude").html(idata["longitude"]);
        $(".item-latitude").html(idata["latitude"]);
        $(".item-address").html(idata["address"]);
        $(".item-expressType").html(idata["expressType"]);
        $(".item-created").html(idata["itemCreated"]);
        $(".item-description").html(idata["itemIntroduction"]);
        $(".item-rating").html(idata["rating"]);
        $(".item-profile").css("background-image", "url('"+idata["itemImageSrc"]+"')");

        var user = idata["user"];
        $(".main-item-footer").attr("data-iid", idata["iid"]);

        if(user["userId"] == getUID()){
            if(idata["status"] == 0){
            }else if(idata["status"] == 1){
                $(".lend-book").removeClass("hide");
            }else{
                $(".return-book").removeClass("hide");
            }
        }else{
            if(idata["status"] == 0){
                $(".borrow-book").removeClass("hide");
            }else{
                $(".already-borrowed").removeClass("hide");
            }
        }
        $(".profile-pic-img-icon").attr("src", user["pic"]).attr("onclick", "openUserById("+user["userId"]+")");
        $(".person-profile-data").attr("data-uid", user["userId"]);
        $(".profile-pic-name").html(user["fname"]).attr("onclick", "openUserById("+user["userId"]+")");
        $(".item-comments").html("");
        var comments = idata["comments"];
        for (var key in comments) {
            if (comments.hasOwnProperty(key)) {
                var commentValue = comments[key];
                var comment = '<div class="item-comment"><div class="comment-header"><div class="profile-pic-icon" onclick="openUserById('+commentValue["uid"]+');"><img class="profile-pic-img-icon" src="'+commentValue["pic"]+'"></div><div class="profile-pic-name" onclick="openUserById('+commentValue["uid"]+');">'+commentValue["fname"]+'</div><div class="commented-date">'+commentValue["commentCreated"]+'</div></div><div class="comment-bubble"><div class="comment-data">'+commentValue["comment"]+'</div></div></div>';
                $(".item-comments").html($(".item-comments").html() + comment);
            }
        }

        $(".nav-on").removeClass("nav-on");
        $("#itempage-iid").val(idata["iid"]);
        setPageTitle(idata["itemName"]);
        return true;
    }
    catch(e){
        return false;
    }
}
function footerIconClick(val){
    var v = $(".main-item-footer").attr("data-iid");
    var formData = new FormData();
    if(v){
        formData.append("itemId", v);
        if(val == "borrow"){
            openBorrowPage(v);
        }
        else if(val == "lend"){
            postData("lend", formData, true);
        }
        else if(val == "return"){
            postData("return", formData, true);
        }
    }
}
function openBorrowPage(data){
    var iid = data;
    $(".borrow-item-id").val(iid);
    hideFooter();
    showThisPage("borrowpage");
}
function allPages(){
    var data = ["mainpage", "publishpage", "userpage", "itempage", "mappage", "searchpage", "borrowpage", "notificationpage"];
    return data;
}
function hideAllPages(){
    var allpages = allPages();
    for (i in allpages){
        var className = "#"+allpages[i];
        $(className).css("display", "none");
    }
}
function showThisPage(val){
    hideAllPages();
    $("#"+val).css("display", "");
    setBoradPageTitle(val);
}
function publishThisItem(form){
    var data = new FormData(form);
    postData("publish", data, false);
    return false;
}
function setBoradPageTitle(val){
    var data = {
        "mainpage": "Main Page",
        "publishpage": "Publish Item",
        "mappage": "Map",
        "borrowpage": "Borrow Page"
    }
    if(data[val]){
        setPageTitle(data[val]);
    }
}
function setPageTitle(val){
    $(".page-title").html(val);
}
function logOff(){
    window.localStorage.removeItem("uid");
    window.localStorage.removeItem("phash");
    window.location = window.getMobileBase()+"/"+getLoginPageUrl();
}
function openProfilePage(){
    var formData = new FormData();
    formData.append("userId", getUID());
    postData("user", formData, true);

    var f = new FormData();
    postData("notification", f, true);
}
function getDistanceFromHere(val){
    var v = getCurrentLocation();
    return getDistance(val["longitude"], val["latitude"], v["longitude"], v["latitude"]);
}
function getDistance(lon1, lat1, lon2, lat2){
    var R = 6371000; // metres
    var φ1 = lat1.toRadians();
    var φ2 = lat2.toRadians();
    var Δφ = (lat2-lat1).toRadians();
    var Δλ = (lon2-lon1).toRadians();

    var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
        Math.cos(φ1) * Math.cos(φ2) *
        Math.sin(Δλ/2) * Math.sin(Δλ/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    var d = R * c;
}
function getCurrentLocation(){
    locate();
    return locati;
}
function createMap(){
    var loc = getCurrentLocation();
    map = new GMaps({
        div: '#map',
        zoom: 16,
        lng: loc.longitude,
        lat: loc.latitude,
        click: function(e){
            window.addMarker(e.latLng.lng(), e.latLng.lat());
        }.bind(window)
    });
}

var locati = {"longitude": 121.59854650497437, "latitude": 31.19125417669881};
function takeMeToTheMap(){
    if(marker){map.removeMarker(marker);}
    loadPage("mappage", {});
}
function addMarker(lng, lat){
    if(marker){map.removeMarker(marker);}
    marker = map.addMarker({
        lng: lng,
        lat: lat,
        title: "Item Address"
    });
    $("#publish-lng").val(lng);
    $("#publish-lat").val(lat);
    $(".map-btn").html("Location Already Set");
    url = "http://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+lng+"&sensor=true";
    $.ajax({
        method: "GET",
        url: url,
        success: function(data){
            var res = data["results"];
            if(!res){return false;}
            var j = 0;
            for (i in res){
                if (i == j){
                    var add = res[i];
                    if(add){
                        var comp = add["formatted_address"];
                        if(!comp){j = 1;continue;}
                        $("#publish-address").val(comp);

                    }
                }
            }
        }.bind(window)
    });
}
function locate(){
    GMaps.geolocate({
        success: function(position) {
            window.locati = {"latitude": position.coords.latitude, "longitude": position.coords.longitude};
        }.bind(window)
    });
}
function borrowThatBookNow(){
    var itemId = $(".borrow-item-id").val();
    var message = $(".borrow-message").val();
    var formData = new FormData();
    formData.append("message", message);
    formData.append("itemId", itemId);
    postData("borrow", formData, true);
}
function manageNotification(data){
    var notifications = data["data"];
    if(!notifications){return false;}
    var leng = 0;
    var val = '';
    for (i in notifications){
        if(notifications.hasOwnProperty(i)){
            leng = leng + 1;
            val += getNotificationData(notifications[i]);
        }
    }
    console.log(leng);
    if(leng > 0){
        if(leng < 9){
            leng = " " + leng;
        }
        $(".notification-data-circle").html(leng);
        $(".notification-item").removeClass("hide");
        $(".notificationpage-list").html(val);
    }
    if(leng == 0){
        $(".notification-data-circle").html("");
        $(".notification-item").addClass("hide");
        $(".notificationpage-list").html("");
    }
}
function getNotificationData(data){
    var val = '';
    try{
        val += '<div class="notification" onclick="openItemById('+data["iid"]+')"><div class="item-image-icon"><img class="item-img" src="'+data["itemImageSrc"]+'"></div><div class="item-detail">';
        val += '<div class="userN-name" onclick="openUserById('+data["uid"]+')">'+data["uid"]+' wants this.</div>';
        val += '<div class="noti-mess" style="overflow: hidden;">Message: <i class="">'+data["message"]+'</i></div></div></div>';
    }
    catch(e){
        val = '';
    }
    return val;
}