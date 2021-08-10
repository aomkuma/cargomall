angular.module('app').controller('TrackingNoneOwnerDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, $routeParams, HTTPService, IndexOverlayFactory) {
  
    $templateCache.removeAll();
    $scope.clearTimeout();
    $scope.id = $routeParams.id;

    $scope.getData = function(){
      var params = {'id' : $scope.id};
      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('tracking-none-owner/get/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.Data = result.data.DATA.Data;
          $scope.OrderTrackingDataNoneOwner = result.data.DATA.OrderTrackingData;
          $scope.CustomerRequestOwner = result.data.DATA.CustomerRequestOwner;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.addToOwner = function(id){

      $scope.alertMessage = 'ต้องการแจ้งเป็นเจ้าของ ใช่หรือไม่ ?';

      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/dialog_confirm.html',
          size : 'sm',
          scope : $scope,
          backdrop : 'static',
          controller : 'ModalDialogCtrl',
          resolve : {
              params : function() {
                  return {};
              } 
          },
      });
      modalInstance.result.then(function (valResult) {
          $scope.confirmaddToOwner(id);
      });
    }

    $scope.confirmaddToOwner = function(id){

      var params = {'id' : id};

      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('tracking-none-owner/request-to-be-owner',params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // $scope.AddressList = result.data.DATA;
          window.location.href = 'tracking-none-owner/list';
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }
    

    $scope.condition = {'tracking_no' : ''};

    $scope.getData();

});
