angular.module('app').controller('ForgotPassController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
        $scope.clearTimeout();
    window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-161508645-1');
    $templateCache.removeAll();

    $scope.checkUrl = function(){
    	IndexOverlayFactory.overlayShow();
	    var params = {'data_key' : $scope.data_key};

	    HTTPService.clientRequest('forgot-pass/check', params).then(function(result){
	        if(result.data.STATUS == 'OK'){
	        	$scope.Data.email = result.data.DATA.email;
	        }else{
	          var alertMsg = result.data.DATA;
	          alert(alertMsg);
	          history.back();
	        }
	        IndexOverlayFactory.overlayHide();
	     });
    }

    $scope.updateData = function(){
    	var Data = angular.copy($scope.Data);
    	IndexOverlayFactory.overlayShow();
    	if(Data.password != Data.confirm_password){
	      alert($filter('translate')('INVALID_CONFIRMPASS_TXT'));
	      IndexOverlayFactory.overlayHide();
	      return false;
	    }

	    var params = {'data_key' : $scope.data_key, 'email' : Data.email, 'password' : Data.password};

	    HTTPService.clientRequest('forgot-pass/update', params).then(function(result){
	        if(result.data.STATUS == 'OK'){
	        	alert('แก้ไขรหัสผ่านสำเร็จ');
	        	window.location.href = '';
	        }else{
	          var alertMsg = result.data.DATA;
	          alert(alertMsg);
	          history.back();
	        }
	        IndexOverlayFactory.overlayHide();
	     });
    }

    $scope.Data = {'email' : '' , 'password' : ''};
    $scope.data_key = $routeParams.data_key;

    $scope.checkUrl();

});
