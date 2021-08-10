angular.module('app').controller('TrackingNoneOwnerController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	
	
    $scope.clearTimeout();
    $templateCache.removeAll();

    $scope.getList = function(){
      var params = {'condition' : $scope.condition};
      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('tracking-none-owner/list/active', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.DataList = result.data.DATA.DataList;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.condition = {'tracking_no' : ''};
    $scope.DataList = [];

    $scope.getList();

});
