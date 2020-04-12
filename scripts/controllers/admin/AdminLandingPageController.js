angular.module('app').controller('AdminLandingPageController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory) {
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

    $scope.getList = function(){
      IndexOverlayFactory.overlayShow();
      var params = {'condition' : $scope.condition
                    , 'currentPage': $scope.Pagination.currentPage
                    , 'limitRowPerPage': $scope.Pagination.limitRowPerPage
      };
      HTTPService.clientRequest('admin/landing-page/list', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          
          $scope.DataList =  result.data.DATA.DataList;
          $scope.Pagination.totalPages = result.data.DATA.Total;
          
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

    $scope.condition = {'user_id' : null, 'pay_type' : null};
    // 1 = ชำระค่าสินค้าบริการ 2 = ชำระค่าขนส่ง, 3=โอนเงินไปจีน, 4=ฝากจ่าย, 5=นำเข้าสินค้า
    $scope.Pagination = {'totalPages' : 0, 'currentPage' : 0, 'limitRowPerPage' : 15, 'limitDisplay' : 10};

    $scope.getList();

});
