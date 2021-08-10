angular.module('app').controller('AdminTransportRateDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $routeParams, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	    $scope.clearTimeout();
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

    $scope.loadData = function(){

        IndexOverlayFactory.overlayShow();
        var params = {'rate_level' : $scope.rate_level};
        HTTPService.clientRequest('admin/transport-rate/get', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.Data = result.data.DATA;
            }else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.updateData = function(Data){

        IndexOverlayFactory.overlayShow();
        var params = {'Data' : Data};
        HTTPService.clientRequest('admin/transport-rate/update', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                if(result.data.DATA == 'add'){
                  window.location.reload();
                }
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }


  $scope.updateRateLevelName = function(Data){

        IndexOverlayFactory.overlayShow();
        var params = {'Data' : Data};
        HTTPService.clientRequest('admin/transport-rate/update/rate-level', params).then(function(result){
            if(result.data.STATUS == 'OK'){
                window.location.href = 'admin/transport-rate/detail/' + $scope.TransportRate.rate_level;
                
            }
            else{
              var alertMsg = result.data.DATA;
              alert(alertMsg);
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.TransportRate = {'rate_level' : $routeParams.rate_level, 'rate_by_condition' : 'kg'};
    $scope.rate_level = null;
    
    if(checkEmptyField($routeParams.rate_level)){
      $scope.rate_level = $routeParams.rate_level;
    }

    $scope.loadData();

});
