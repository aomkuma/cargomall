angular.module('app').controller('ImporterMainController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
        $scope.clearTimeout();
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    if(!checkEmptyField($scope.session_storage.user_data)){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      // window.location.replace('');
      history.back();
      return;
    }

    $scope.list = function(){

    	IndexOverlayFactory.overlayShow();
    	var params = {'condition' : $scope.condition};
    	HTTPService.clientRequest('importer/list/by-user', params).then(function(result){
	        if(result.data.STATUS == 'OK'){
	        	$scope.DataList = result.data.DATA;

	        	for(var i = 0; i < $scope.DataList.length; i++){

	        		// $scope.DataList[i].total_price_thb = 0;
	        		// $scope.DataList[i].package_price = 0;
	        		// $scope.DataList[i].total_price_yuan = 0;
	        		// $scope.DataList[i].discount = 0;

	        		if(checkEmptyField($scope.DataList[i].total_price_thb)){
	        			$scope.DataList[i].total_price_thb = parseFloat($scope.DataList[i].total_price_thb);
	        		}
	        		if(checkEmptyField($scope.DataList[i].package_price)){
	        			$scope.DataList[i].package_price = parseFloat($scope.DataList[i].package_price);
	        		}
	        		if(checkEmptyField($scope.DataList[i].total_price_yuan)){
	        			$scope.DataList[i].total_price_yuan = parseFloat($scope.DataList[i].total_price_yuan);
	        		}
	        		if(checkEmptyField($scope.DataList[i].discount)){
	        			$scope.DataList[i].discount = parseFloat($scope.DataList[i].discount);
	        		}
	        		
	        	}
	        }else{
	          var alertMsg = result.data.DATA;
	          alert(alertMsg);
	        }
	        IndexOverlayFactory.overlayHide();
	     });
    }

    $scope.condition = {'tracking_no' : ''};

    $scope.list();

});
