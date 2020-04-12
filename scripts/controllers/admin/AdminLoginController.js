angular.module('app').controller('AdminLoginController',function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, $location, HTTPService, IndexOverlayFactory){
	
	$scope.Login = {'email':'','password':''};

    $scope.checkAdminLogin = function(loginObj){
      IndexOverlayFactory.overlayShow();
      var params = {'LoginObj' : loginObj};
      HTTPService.clientRequest('admin/login', params).then(function(result){
        if(result.data.STATUS == 'OK'){
          // $scope.closeLoginDialog();
          $localStorage.$default({'token' : result.data.DATA.token, 'user_data' : result.data.DATA.UserData});
          // $scope.UserDara = result.data.DATA.UserData;
          window.location.replace('admin/home');
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
  }

});
