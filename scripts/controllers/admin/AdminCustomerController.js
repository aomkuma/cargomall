angular.module('app').controller('AdminCustomerController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getUserList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition
                    , 'currentPage': $scope.Pagination.currentPage
                    , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
      };
      HTTPService.clientRequest('admin/customer/list', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.UserList =  result.data.DATA.DataList;
          $scope.Pagination.totalPages = result.data.DATA.Total;
                
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.goToPage = function(page){
        $scope.getUserList();
    }

    $scope.pageChanged = function() {
        $scope.goToPage($scope.currentPage);
    };

    $scope.getPayType = function(pay_type){
      if(pay_type == 1){
        return 'ชำระค่าสินค้าบริการ';
      }else if(pay_type == 2){
        return 'ชำระค่าขนส่ง';
      }else if(pay_type == 3){
        return 'โอนเงินไปจีน';
      }else if(pay_type == 4){
        return 'ฝากจ่าย';
      }else if(pay_type == 5){
        return 'นำเข้าสินค้า';
      }
    }

    $scope.withdrawnMoney = function(money_Bag){
      $scope.MoneyBag = angular.copy(money_Bag);
      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/admin/money-bag.html',
          size : 'md',
          scope : $scope,
          backdrop : 'static',
          controller : 'ModalDialogReturnFromOKBtnCtrl',
          resolve : {
              params : function() {
                  return {};
              } 
          },
      });
      modalInstance.result.then(function (valResult) {
          $scope.updateWithdrawnMoney(valResult);
      });
    }

    $scope.updateWithdrawnMoney = function(WithdrawnData){
      IndexOverlayFactory.overlayShow();
      var params = {'WithdrawnData' : WithdrawnData};
      HTTPService.clientRequest('admin/customer/withdrawn', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          window.location.reload();
        }else{
          alert(result.data.DATA);
          return false;
        }

        IndexOverlayFactory.overlayHide();
      }); 
    }

    $scope.refundMoney = function(money_Bag){
      $scope.MoneyBag = angular.copy(money_Bag);
      var modalInstance = $uibModal.open({
          animation : false,
          templateUrl : 'views/admin/refund.html',
          size : 'md',
          scope : $scope,
          backdrop : 'static',
          controller : 'ModalDialogReturnFromOKBtnCtrl',
          resolve : {
              params : function() {
                  return {};
              } 
          },
      });
      modalInstance.result.then(function (valResult) {
          $scope.updateRefundMoney(valResult);
      });
    }

    $scope.updateRefundMoney = function(RefundData){
      IndexOverlayFactory.overlayShow();
      var params = {'RefundData' : RefundData};
      HTTPService.clientRequest('admin/customer/refund', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          window.location.reload();
        }else{
          alert(result.data.DATA);
          return false;
        }

        IndexOverlayFactory.overlayHide();
      }); 
    }

    $scope.condition = {'user_id' : null, 'pay_type' : null};
    // 1 = ชำระค่าสินค้าบริการ 2 = ชำระค่าขนส่ง, 3=โอนเงินไปจีน, 4=ฝากจ่าย, 5=นำเข้าสินค้า
    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 50, 'limitDisplay' : 10};

    $scope.getUserList();

});
