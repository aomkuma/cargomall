angular.module('app').controller('AdminTransferController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getTransferList = function(){
    	IndexOverlayFactory.overlayShow();
  		var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
  		HTTPService.clientRequest('admin/transfer/list', params).then(function(result){
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
        $scope.getTransferList();
    }

    $scope.pageChanged = function() {
        $scope.goToPage($scope.currentPage);
    };

    $scope.getTransferStatus = function(transfer_status){
      if(transfer_status == 1){
        return 'คำขอแจ้งโอนเงิน';
      }else if(transfer_status == 2){
        return 'อนุมัติการโอนเงิน (สำเร็จ)';
      }else if(transfer_status == 3){
        return 'ปฏิเสธการโอนเงิน (ยกเลิก)';
      }
    }

    $scope.approveTransfer = function(transfer_id){

      $scope.alertMessage = 'ต้องการอนุมัติการแจ้งโอนเงินนี้ ใช่หรือไม่ ?';

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
          $scope.updateTransferStatus(transfer_id, 2);
      });
    }

    $scope.rejectTransfer = function(transfer_id){

      $scope.alertMessage = 'ต้องการอนุมัติการแจ้งโอนเงินนี้ ใช่หรือไม่ ?';

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
          $scope.updateTransferStatus(transfer_id, 3);
      });
    }

    $scope.updateTransferStatus = function(transfer_id, transfer_status){
        IndexOverlayFactory.overlayShow();
        var params = {'transfer_id' : transfer_id
                  , 'transfer_status': transfer_status
                };
        HTTPService.clientRequest('admin/transfer/status/update', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.uploadSlip = function(data){

      var params = {'id' : data.id, 'AttachFile' : data.AttachFile};

      HTTPService.uploadRequest('admin/transfer/upload', params).then(function(result){
        if(result.data.STATUS == 'OK'){

          $scope.getTransferList();

        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.condition = {'user_id' : null, 'pay_status' : '1'};

    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getUserList();
    $scope.getTransferList();


});
