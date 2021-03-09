angular.module('app').controller('AdminTrackingNoneOwnerDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, $routeParams, HTTPService, IndexOverlayFactory) {
	
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

    $scope.id = $routeParams.id;

    $scope.getData = function(){
      var params = {'id' : $scope.id};
      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('tracking-none-owner/get/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.Data = result.data.DATA.Data;
          $scope.OrderDrackingData = result.data.DATA.OrderDrackingData;
          $scope.CustomerRequestOwner = result.data.DATA.CustomerRequestOwner;
          $scope.OrderData = result.data.DATA.OrderData;
          
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
      HTTPService.clientRequest('admin/carg-address/update/manage', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // $scope.AddressList = result.data.DATA;
          
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

    $scope.uploadImage = function(id){
      IndexOverlayFactory.overlayShow();
      var params = {'id' : id, 'AttachFile' : $scope.AttachFile};
      HTTPService.uploadRequest('tracking-none-owner/upload-image', params).then(function(result){
        if(result.data.STATUS == 'OK'){

          $scope.Data.image_path = result.data.DATA.image_path;
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.addToOwner = function(data_id, user_id, name){

      $scope.alertMessage = 'ต้องการให้คุณ ' + name + ' เป็นเจ้าของ ใช่หรือไม่ ?';

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
          $scope.confirmaddToOwner(data_id, user_id);
      });
    }

    $scope.confirmaddToOwner = function(data_id, user_id){

      var params = {'data_id' : data_id, 'user_id' : user_id};

      IndexOverlayFactory.overlayShow();
      HTTPService.clientRequest('tracking-none-owner/add-to-be-owner',params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          // $scope.AddressList = result.data.DATA;
          window.location.href = 'admin/tracking-none-owner/list';
          
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
