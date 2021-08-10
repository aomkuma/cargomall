angular.module('app').controller('AdminBankAccountController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getList = function(){

      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/bank-account/list/manage', null).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.BankAccountList = result.data.DATA.DataList;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.addData = function(){
      $scope.BankAccountList.push({'id' : null, 'account_no' : null, 'account_name' : null, 'bank_name' : null, 'account_type' : null, 'is_active' : true});
    }

    $scope.updateData = function(data){

      var params = {'Data' : data};

      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/bank-account/update/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // $scope.BankAccountList = result.data.DATA;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.updateData = function(data){

      var params = {'Data' : data};

      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('admin/bank-account/update/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // $scope.BankAccountList = result.data.DATA;
          data.id = result.data.DATA.id;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.deleteData = function(index, id){

      if(id == null){
        $scope.BankAccountList.splice(index, 1);
      }else{

        $scope.alertMessage = 'ต้องการลบรายการที่อยู่นี้ ใช่หรือไม่ ?';

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
            $scope.confirmDeleteData(id);
        });
      }
    }

    $scope.confirmDeleteData = function(id){
        IndexOverlayFactory.overlayShow();
        var params = {'id' : id};
        HTTPService.clientRequest('admin/bank-account/delete/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.BankAccountList = [];

    $scope.getList();

});
