angular.module('app').controller('AdminOrderController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    // $log.log($scope.session_storage.user_data);
    if(!checkEmptyField($scope.session_storage.user_data)){
      // alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      window.location.replace('admin/signin');
      // history.back();
      return;
    }

    if(!checkEmptyField($scope.UserData.is_admin) && !$scope.UserData.is_admin){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      history.back();
      return;
    }

    $templateCache.removeAll();

    $scope.getOrderList = function(){
    	IndexOverlayFactory.overlayShow();
  		var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
  		HTTPService.clientRequest('admin/order/list', params).then(function(result){
  		if(result.data.STATUS == 'OK'){
  			
        $scope.DataList =  result.data.DATA.DataList;
        $scope.Pagination.totalPages = result.data.DATA.Total;
                
        for(var i = 0; i < $scope.DataList.length; i++){
          if($scope.DataList[i].order_desc.china_ex_rate == 0){
            $scope.DataList[i].order_desc.china_ex_rate = $scope.exchange_rate;
          }
        }
  		}else{
  		  var alertMsg = result.data.DATA;
  		  alert(alertMsg);
  		}
  		IndexOverlayFactory.overlayHide();
  		});
    }

    $scope.goToPage = function(page){
        $scope.getOrderList();
    }

    $scope.pageChanged = function() {
        $scope.goToPage($scope.currentPage);
    };

    $scope.uploadExcel = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'AttachFile' : $scope.AttachFile};
      HTTPService.uploadRequest('admin/order/upload', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        window.location.reload();
        
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.condition = {'keyword' : ''};

    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getOrderList();

});
