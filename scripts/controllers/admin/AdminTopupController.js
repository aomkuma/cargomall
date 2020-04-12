angular.module('app').controller('AdminTopupController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getTopupList = function(){
    	IndexOverlayFactory.overlayShow();
  		var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
  		HTTPService.clientRequest('admin/topup/list', params).then(function(result){
  		if(result.data.STATUS == 'OK'){
  			
        $scope.DataList =  result.data.DATA.DataList;
        $scope.Pagination.totalPages = result.data.DATA.Total;
                
        /*for(var i = 0; i < $scope.DataList.length; i++){
          if($scope.DataList[i].order_desc.china_ex_rate == 0){
            $scope.DataList[i].order_desc.china_ex_rate = $scope.exchange_rate;
          }
        }*/
  		}else{
  		  var alertMsg = result.data.DATA;
  		  alert(alertMsg);
  		}
  		IndexOverlayFactory.overlayHide();
  		});
    }

    $scope.getUserList = function(){
      HTTPService.clientRequest('admin/user/list', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.UserList =  result.data.DATA.DataList;
        }
      });
    }

    $scope.goToPage = function(page){
        $scope.getTopupList();
    }

    $scope.pageChanged = function() {
        $scope.goToPage($scope.currentPage);
    };

    $scope.getTopupStatus = function(topup_status){
      if(topup_status == 1){
        return 'แจ้งเติมเงิน';
      }else if(topup_status == 2){
        return 'อนุมัติการแจ้งเติมเงิน';
      }else if(topup_status == 3){
        return 'ปฏิเสธการแจ้งเติมเงิน';
      }
    }

    $scope.approveTopup = function(topup_id){

      $scope.alertMessage = 'ต้องการอนุมัติการแจ้งเติมเงินนี้ ใช่หรือไม่ ?';

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
          $scope.updateTopupStatus(topup_id, 2);
      });
    }

    $scope.rejectTopup = function(topup_id){

      $scope.alertMessage = 'ต้องการอนุมัติการแจ้งเติมเงินนี้ ใช่หรือไม่ ?';

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
          $scope.updateTopupStatus(topup_id, 3);
      });
    }

    $scope.updateTopupStatus = function(topup_id, topup_status){
        IndexOverlayFactory.overlayShow();
        var params = {'topup_id' : topup_id
                  , 'topup_status': topup_status
                };
        HTTPService.clientRequest('admin/topup/status/update', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
          window.location.reload();
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.condition = {'topup_status' : '1'};

    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getUserList();
    $scope.getTopupList();

});
