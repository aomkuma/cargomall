angular.module('app').controller('AdminDepositController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getDepositList = function(){
    	IndexOverlayFactory.overlayShow();
  		var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
  		HTTPService.clientRequest('admin/deposit/list', params).then(function(result){
  		if(result.data.STATUS == 'OK'){
  			
        $scope.DataList =  result.data.DATA.DataList;
        $scope.Pagination.totalPages = result.data.DATA.Total;
            
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
        $scope.getDepositList();
    }

    $scope.pageChanged = function() {
        $scope.goToPage($scope.currentPage);
    };

    $scope.getDepositStatus = function(deposit_status){
      if(deposit_status == 1){
        return 'คำขอฝากจ่าย';
      }else if(deposit_status == 2){
        return 'ยืนยันการฝากจ่าย (สำเร็จ)';
      }else if(deposit_status == 3){
        return 'ยกเลิกการฝากจ่าย';
      }
    }

    $scope.approveDeposit = function(deposit_id){

      $scope.alertMessage = 'ต้องการอนุมัติการแจ้งฝากจ่ายนี้ ใช่หรือไม่ ?';

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
          $scope.updateDepositStatus(deposit_id, 2);
      });
    }

    $scope.rejectDeposit = function(deposit_id){

      $scope.alertMessage = 'ต้องการปฏิเสธการแจ้งฝากจ่ายนี้ ใช่หรือไม่ ?';

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
          $scope.updateDepositStatus(deposit_id, 3);
      });
    }

    $scope.updateDepositStatus = function(deposit_id, deposit_status){
        IndexOverlayFactory.overlayShow();
        var params = {'deposit_id' : deposit_id
                  , 'deposit_status': deposit_status
                };
        HTTPService.clientRequest('admin/deposit/status/update', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          window.location.reload();
          
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
    $scope.getDepositList();


});
