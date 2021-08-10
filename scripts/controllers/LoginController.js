angular.module('app').controller('LoginController',function($scope, $routeParams){
	    $scope.clearTimeout();
	$scope.user = {'Username':'','Password':''};

    var reDirect = 'member-dashboard';
    if($routeParams.redirect_url !== undefined){
        reDirect = $routeParams.redirect_url;
        console.log(reDirect);
    }
	$scope.showError = false; // set Error flag
	$scope.showSuccess = false; // set Success Flag
    
	//------- Authenticate function
	$scope.authenticate = function (){

        
		var flag= false;
        $scope.showError = false;
        $scope.showSuccess = true;
        // $scope.$parent.TotalLogin = user.data.DATA.TotalLogin;
        // sessionStorage.setItem('user_session' , JSON.stringify(user.data.DATA.UserData));
        // sessionStorage.setItem('TotalLogin' , $scope.$parent.TotalLogin);
        // sessionStorage.setItem('MenuList' , JSON.stringify(user.data.DATA.MenuList));
        setTimeout(function(){
            window.location.replace('#/' + reDirect);    
        }, 1000);
        
        /*IndexOverlayFactory.overlayShow();
		LoginFactory.login($scope.user).then(function (user) {
            IndexOverlayFactory.overlayHide();
    		//-------- set error or success flags
    		if(user.data.STATUS == 'OK'){
    			$scope.showError = false;
    			$scope.showSuccess = true;
                $scope.$parent.TotalLogin = user.data.DATA.TotalLogin;
                sessionStorage.setItem('user_session' , JSON.stringify(user.data.DATA.UserData));
                sessionStorage.setItem('TotalLogin' , $scope.$parent.TotalLogin);
                sessionStorage.setItem('MenuList' , JSON.stringify(user.data.DATA.MenuList));
                setTimeout(function(){
                    window.location.replace('#/' + reDirect);    
                }, 1000);
                
    		}
    		else{
    			$scope.showError = true;
    			$scope.showSuccess = false;
    		}
            
        });*/

	}

});
