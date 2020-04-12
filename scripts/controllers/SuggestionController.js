angular.module('app').controller('SuggestionController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	//console.log('Hello !');
    // $scope.DEFAULT_LANGUAGE = 'TH';
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
    $templateCache.removeAll();

    $scope.videoURL = 'https://www.youtube.com/watch?v=GUdtEAmrmXc';

    $scope.section = 'calc-transport-cost';
    // $scope.section = 'add-cargo';
});
