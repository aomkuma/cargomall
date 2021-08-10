angular.module('app').controller('ContactController', function($scope, $compile, $cookies, $filter, $state, HTTPService, IndexOverlayFactory) {
        $scope.clearTimeout();
    IndexOverlayFactory.overlayShow();
    console.log('Hello ! Contact page');
	$scope.$parent.menu_selected = 'contact';
    $scope.$parent.menu_selected_th = 'ข้อมูลการติดต่อ';
    $scope.load = function(action){
        HTTPService.clientRequest(action, null).then(function(result){
            console.log(result);
            $scope.Contact = result.data.DATA.Contact;
            IndexOverlayFactory.overlayHide();

        });
    }

    $scope.FileList = [];
    $scope.Contact = {'contents':null};

    $scope.load('contacts');
});