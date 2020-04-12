angular.module('app').controller('AdminHomeController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	
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

    $scope.getOrderPendingList = function(){

      // IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/order/list/pending', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.OrderList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.geImporterPendingList = function(){
      // IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/importer/list/pending', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.ImporterList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.getTopupPendingList = function(){
      // IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/topup/list/pending', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.TopupList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.getTtransferPendingList = function(){
      // IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/transfer/list/pending', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.TransferList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.getDepositPendingList = function(){
      // IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/deposit/list/pending', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.DepositList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.getMonitorPendingList = function(){
      // IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/monitor/pending', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.OrderList = result.data.DATA.order_list;
          $scope.ImporterList = result.data.DATA.importer_list;
          $scope.TopupList = result.data.DATA.topup_list;
          $scope.TransferList = result.data.DATA.transfer_list;
          $scope.DepositList = result.data.DATA.deposit_list;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    
    $scope.getMonitorPendingList();
    setInterval(function(){
      $scope.getMonitorPendingList();
    }, 120000);

    // $scope.getOrderPendingList();
    // $scope.geImporterPendingList();
    // $scope.getTopupPendingList();
    // $scope.getTtransferPendingList();
    // $scope.getDepositPendingList();

    /*
    setInterval(function(){
      $scope.getTopupPendingList();
      $scope.getTtransferPendingList();
      $scope.getDepositPendingList();
    }, 30000);

    setInterval(function(){
      $scope.getOrderPendingList();
      $scope.geImporterPendingList();
    }, 60000);
  */
});
