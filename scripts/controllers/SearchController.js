angular.module('app').controller('SearchController', function($scope, $compile, $cookies, $filter, $state, $routeParams, HTTPService, IndexOverlayFactory) {
    IndexOverlayFactory.overlayShow();
    console.log('Hello ! Palace page');
	
    $scope.$parent.menu_selected = 'Search';
    $scope.$parent.menu_selected_th = 'ค้นหา';

    $scope.loadMenu = function(action){
        HTTPService.clientRequest(action, null).then(function(result){
            //console.log(result);
            $scope.Menu = result.data.DATA.Menu;
            IndexOverlayFactory.overlayHide();
            $(document).ready(function(){
                // console.log('asd');
              $('a.test').on("click", function(e){
                // alert('aa');
                // $('ul.dropdown-menu').hide();
                $(this).next('ul').toggle();
                e.stopPropagation();
                e.preventDefault();
              });
            });

            // $scope.load('menu/page/get', $scope.ID);
            
        });
    }

    $scope.load = function(action){
        var params = {'keyword' : $scope.keyword};
        HTTPService.clientRequest(action, params).then(function(result){
            console.log(result);
            $scope.SearchList = result.data.DATA;
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.keyword = $routeParams.keyword;
    $scope.loadMenu('menu/list');
    $scope.load('search');

});