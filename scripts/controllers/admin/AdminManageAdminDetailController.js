angular.module('app').controller('AdminManageAdminDetailController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, $routeParams, HTTPService, IndexOverlayFactory) {
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

    $scope.getData = function(){
    	IndexOverlayFactory.overlayShow();
  		var params = {'id' : $scope.id };
  		HTTPService.clientRequest('admin/manage-admin/get', params).then(function(result){
  		if(result.data.STATUS == 'OK'){
  			
        $scope.Data =  result.data.DATA;
        $scope.Data.role = parseInt($scope.Data.role);
  		}else{
  		  var alertMsg = result.data.DATA;
  		  alert(alertMsg);
  		}
  		IndexOverlayFactory.overlayHide();
  		});
    }

    $scope.updateData = function(Data){

      var UpdateData = angular.copy(Data);

      if($scope.id == null){
        if(UpdateData.password != UpdateData.confirm_password){
          alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
          return false;
        }
      }else if(checkEmptyField(UpdateData.new_password) && (UpdateData.new_password != UpdateData.confirm_password)){
        alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
        return false;
      }

      IndexOverlayFactory.overlayShow();
      var params = {'Data' : UpdateData };
      HTTPService.clientRequest('admin/manage-admin/update', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        if($scope.UserData.role == 1){
          window.location.href = 'admin/manage-admin';
        }else{
          window.location.href = 'admin';
        }
        

      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.Data = {'id' : '', 'password' : null, 'role' : 2, 'active_status' : 'Y'};
    $scope.Role = [{'id' : 1, 'value' : 'แอดมินสูงสุด'}, {'id' : 2, 'value' : 'แอดมินทั่วไป'}];
    $scope.id = null;
    if(checkEmptyField( $routeParams.id )){
      $scope.id = $routeParams.id;
      $scope.getData();
    }

});
