angular.module('app').controller('AdminTrackingNoneOwnerController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
	
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
      var params = {'condition' : $scope.condition};
      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('tracking-none-owner/list/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.DataList = result.data.DATA.DataList;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.deleteData = function(index, id){

      if(id == null){
        $scope.AddressList.splice(index, 1);
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
        HTTPService.clientRequest('admin/carg-address/delete/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
        });
    }

    $scope.uploadImage = function(id){
      var params = {'id' : id, 'AttachFile' : AttachFile};
      HTTPService.uploadRequest('tracking-none-owner/upload-image', params).then(function(result){
        if(result.data.STATUS == 'OK'){

          $scope.Data.image_path = result.data.DATA;
          
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
