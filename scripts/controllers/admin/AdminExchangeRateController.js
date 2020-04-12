angular.module('app').controller('AdminExchangeRateController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	
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

    $scope.getExchangeRateList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
      HTTPService.clientRequest('admin/exchange-rate/list', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        $scope.DataList =  result.data.DATA.DataList;
        $scope.Pagination.totalPages = result.data.DATA.Total;
        
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.addExchangeRate = function(ExchangeRate){

      var ExchangeRate = angular.copy(ExchangeRate);

      IndexOverlayFactory.overlayShow();
      var params = {'ExchangeRate' : $scope.ExchangeRate
    };
      HTTPService.clientRequest('admin/exchange-rate/update', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        window.location.reload();
        
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

    $scope.condition = {'created_at' : null};
    $scope.ExchangeRate = {'exchange_rate' : null};

    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 30, 'limitDisplay' : 10};

    $scope.getExchangeRateList();


    // Exchange Rate Transfer

    $scope.getExchangeRateTransferList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition_transfer
                  , 'currentPage': $scope.PaginationTransfer.currentPage
                  , 'limitRowPerPage': $scope.PaginationTransfer.limitRowPerPage
                };
      HTTPService.clientRequest('admin/exchange-rate-transfer/list', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        $scope.DataListTransfer =  result.data.DATA.DataList;
        $scope.PaginationTransfer.totalPages = result.data.DATA.Total;
        
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.addExchangeRateTransfer = function(ExchangeRate){

      var ExchangeRate = angular.copy(ExchangeRate);

      IndexOverlayFactory.overlayShow();
      var params = {'ExchangeRate' : $scope.ExchangeRateTransfer
    };
      HTTPService.clientRequest('admin/exchange-rate-transfer/update', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        window.location.reload();
        
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.goToPageTransfer = function(page){
        $scope.getOrderList();
    }

    $scope.pageChangedTransfer = function() {
        $scope.goToPageTransfer($scope.currentPage);
    };

    $scope.condition_transfer = {'created_at' : null};
    $scope.ExchangeRateTransfer = {'exchange_rate' : null};

    $scope.PaginationTransfer = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 30, 'limitDisplay' : 10};

    $scope.getExchangeRateTransferList();

});
