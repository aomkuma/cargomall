angular.module('app').controller('MemberStatementController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $routeParams, HTTPService, IndexOverlayFactory) {
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
        $scope.getPayList();
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

    $scope.getTopupStatus = function(topup_status){
      if(topup_status == 1){
        return 'แจ้งเติมเงิน';
      }else if(topup_status == 2){
        return 'อนุมัติการแจ้งเติมเงิน';
      }else if(topup_status == 3){
        return 'ปฏิเสธการแจ้งเติมเงิน';
      }
    }


    // Deposit

    $scope.getTopupList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.topup_condition
                  , 'currentPage': $scope.TopupPagination.currentPage
                  , 'limitRowPerPage': $scope.TopupPagination.limitRowPerPage
                };
      HTTPService.clientRequest('topup/history', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        $scope.TopupDataList =  result.data.DATA.DataList;
        $scope.TopupPagination.totalPages = result.data.DATA.Total;
              
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.goToTopupPage = function(page){
        $scope.getTopupList();
    }

    $scope.pageTopupChanged = function() {
        $scope.goToTopupPage($scope.currentPage);
    };


    // Refund
    $scope.getRefundList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.refund_condition
                  , 'currentPage': $scope.RefundPagination.currentPage
                  , 'limitRowPerPage': $scope.RefundPagination.limitRowPerPage
                };
      HTTPService.clientRequest('refund/history', params).then(function(result){
      if(result.data.STATUS == 'OK'){
        
        $scope.RefundDataList =  result.data.DATA.DataList;
        $scope.RefundPagination.totalPages = result.data.DATA.Total;
              
      }else{
        var alertMsg = result.data.DATA;
        alert(alertMsg);
      }
      IndexOverlayFactory.overlayHide();
      });
    }

    $scope.goToRefundPage = function(page){
        $scope.getRefundList();
    }

    $scope.pageRefundchanged = function() {
        $scope.goToRefundPage($scope.currentPage);
    };

    $scope.condition = {'user_id' : null, 'pay_type' : null};
    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.topup_condition = {'user_id' : null, 'pay_type' : null};
    $scope.TopupPagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.refund_condition = {'user_id' : null, 'pay_type' : null};
    $scope.RefundPagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getPayList();
    $scope.getTopupList();
    $scope.getRefundList();

});
