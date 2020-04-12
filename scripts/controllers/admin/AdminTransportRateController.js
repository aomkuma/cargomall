angular.module('app').controller('AdminTransportRateController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $routeParams, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	
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
        var params = null;
        HTTPService.clientRequest('admin/transport-rate/list', params).then(function(result){
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

    $scope.TransportRate = {'rate_by_condition' : 'kg'};

    $scope.loadData();

});
