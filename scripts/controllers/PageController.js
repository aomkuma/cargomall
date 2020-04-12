angular.module('app').controller('PageController', function($scope, $compile, $cookies, $filter, $state, $routeParams, HTTPService, IndexOverlayFactory) {
    IndexOverlayFactory.overlayShow();
    console.log('Hello ! About page');
	
    console.log($scope.DEFAULT_LANGUAGE);
    $scope.ID = $routeParams.id;
    $scope.$parent.menu_selected = $scope.ID;
    $scope.load = function(action, menu_id){
        var params = {'menu_id': menu_id};
        HTTPService.clientRequest(action, params).then(function(result){
            console.log(result);
            $scope.Page = result.data.DATA.Page;
            $scope.FileList = result.data.DATA.FileList;
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.loadPage = function(action, menu_id){
        var params = {'menu_id' : menu_id};
        HTTPService.clientRequest(action, params).then(function(result){
            //console.log(result);

            if(result.data.STATUS == 'OK'){
                $scope.Page = result.data.DATA.Page;
                
            }
        });
    }

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

            $scope.load('menu/page/get', $scope.ID);
            
        });
    }

    $scope.getMenu = function(action, menu_type){
        var params = {'menu_type' : menu_type};
        HTTPService.clientRequest(action, params).then(function(result){
            console.log(result);
            $scope.MenuName = result.data.DATA.Menu;
            // $scope.loadPage('page/get', $scope.MenuName.id);
            IndexOverlayFactory.overlayHide();
        });
    }

    $scope.findExtension = function(filename){
        return (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename)[0] : '';
    }

    $scope.FileList = [];
    $scope.Page = {'contents':null};
    $scope.loadMenu('menu/list');
    $scope.getMenu('menu/get/type' ,$scope.ID);
    
})
;