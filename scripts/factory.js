function returnResponse(response){
   if(!tryParseJSON(response.data)){

      //the json is ok
        return response.data;
    }else{

      //the json is not ok
        // console.log(response.data);
        return false;
    }
}

function returnErrorResponse(errResponse){
    var errorDesc = errResponse.status + ' : ' + errResponse.statusText ;
    
    if(errResponse.data.DATA != null){
        errorDesc += ':: Cause ' + errResponse.data.DATA.errorInfo[2];
        alert( errorDesc );
    }else{
        if(errResponse.status == 401){
            alert( 'ไม่มีสิทธิ์เข้าใช้งานในหน้านี้' );
            window.location.replace("#/");
        }else if(errResponse.status == 500){
            
            alert('ระบบเกิดข้อขัดข้อง กรุณาตรวจสอบข้อมูลและทำรายการใหม่อีกครั้ง');//alert(errResponse.data.message);
            // return false;
            
        }else if(errResponse.status == 524){
            
            alert('ระบบเกิดข้อขัดข้อง กรุณาตรวจสอบข้อมูลและทำรายการใหม่อีกครั้ง');
            // return false;
            
        }else{
            alert( errorDesc );
        }
    }

    console.error('Error while fetching specific Item', errResponse);
    return errResponse;
}

function tryParseJSON (jsonString){
    try {
        var o = JSON.parse(jsonString);
        if (o && typeof o === "object") {
            return o;
        }
    }
    catch (e) { //alert(jsonString); 
    }

    return false;
};

app.factory('IndexOverlayFactory', function(){
    var indexVar = 
    {
        overlay : false,
        overlayHide : function() {this.overlay = false},
        overlayShow : function() {this.overlay = true},

        overlay_progressbar : false,
        overlay_timer : 2,
        overlayProgressBarHide : function() {this.overlay_progressbar = false},
        overlayProgressBarShow : function() {
            this.overlay_progressbar = true;
            // this.overlay_timer = n;
            // console.log('timer count..' + n);

        },
        
    };  
    
    return indexVar;
});

app.factory('HTTPService', ['$http', '$q', '$cookies', 'Upload', function($http, $q, $cookies, Upload){

    return {

        externalGetRequest : function(url) {
            return $http.get(url)
                .then(
                    function(response){
                        return returnResponse(response);                    
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        loginRequest : function(action, obj) {
            return $http.post(serviceLoginUrl + action + '/',{"obj_login":obj})
                .then(
                    function(response){
                        return returnResponse(response);                    
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        clientRequest : function(action, obj) {
            // $scope.$storage = $localStorage;
            var user_session = angular.fromJson($cookies.get('user_session'));//angular.fromJson($localStorage);//angular.fromJson(sessionStorage.getItem('user_session'));
            // console.log(user_session);

            if(user_session == undefined){
                user_session = {'user_session' : {'token' : null}, 'token' : null};
            }
            $http.defaults.headers.common['Authorization'] = 'Bearer ' + user_session.token;
            return $http.post(serviceUrl + action,{"obj":obj, 'user_session' : user_session, 'token' : user_session.token})
                .then(
                    function(response){
                        return returnResponse(response);                    
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        deleteRequest : function(action, id) {
            return $http.delete(serviceUrl + action + '/' + id)
                .then(
                    function(response){
                        return returnResponse(response);                    
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        },

        uploadRequest : function(action, obj) {
            // $scope.$storage = $localStorage;
            var user_session = angular.fromJson($cookies.get('user_session'));//angular.fromJson($localStorage);//angular.fromJson(sessionStorage.getItem('user_session'));
            // console.log(user_session);
            return Upload.upload({
                url: serviceUrl + action,
                data: {"obj":obj/*, 'user_session' : user_session, 'token' : user_session.token*/}
            }).then(
                function(response){
                    return returnResponse(response);                    
                }, 
                function(errResponse){
                    return returnErrorResponse(errResponse);
                }
            );
        }
    }
}]);
