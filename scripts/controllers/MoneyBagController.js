angular.module('app').controller('MoneyBagController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
	    $scope.clearTimeout();
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    if(!checkEmptyField($scope.session_storage.user_data)){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      // window.location.replace('');
      history.back();
      return;
    }

    $scope.getPayList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition
                  , 'currentPage': $scope.Pagination.currentPage
                  , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
                };
      HTTPService.clientRequest('pay/history', params).then(function(result){
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

    $scope.goToPage = function(page){
        $scope.getOrderList();
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

    $scope.getPayStatus = function(status){
      if(status == 1){
        return 'กำลังดำเนินการ';
      }else if(status == 2){
        return 'สำเร็จ';
      }else if(status == 3){
        return 'ยกเลิก';
      }
    }

    $scope.condition = {'user_id' : null, 'pay_type' : null};
    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getPayList();

});
