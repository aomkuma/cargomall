angular.module('app').controller('TopupInformController', function($scope, $cookies, $filter, $state, $sce, $uibModal, $templateCache, $localStorage, $log, HTTPService, IndexOverlayFactory) {
	
    window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-161508645-1');
  
    $templateCache.removeAll();

    if(!checkEmptyField($scope.session_storage.user_data)){
      alert('คุณไม่มีสิทธิ์ใช้งานเมนูนี้');
      // window.location.replace('');
      history.back();
      // return;
    }

    $scope.topup = function(){
      // alert('pay!');
      $scope.displayOverlay();
      $scope.ShowDialogConfirmTopup = true;
      // return;
      // $log.log($scope.$parent.UserData);return;
      
    }

    $scope.closeConfirmTopupDialog = function(){
      $scope.ShowDialogConfirmTopup = false;
      $scope.hideOverlay();
    }

    $scope.confirmTopup = function(Data, AttachFile){
      $scope.closeConfirmTopupDialog();
      IndexOverlayFactory.overlayShow();
      // $log.log(Data.topup_date);return;
      var TopupData = angular.copy(Data);
      Data.topup_date.setHours($scope.Hour);
      Data.topup_date.setMinutes($scope.Minute);
      TopupData.topup_date = concatDateTimeSQL(Data.topup_date, Data.topup_date);
      TopupData['user_id'] = $scope.$parent.UserData.id;
      var params = {'Data' : TopupData, 'AttachFile' : AttachFile};
      HTTPService.uploadRequest('topup/inform', params).then(function(result){
        if(result.data.STATUS == 'OK'){

          $localStorage.user_data = (result.data.DATA);
          $scope.UserData = angular.copy(angular.fromJson(atob(result.data.DATA)));

          $scope.TopupSuccess = true;
          // return;
          setTimeout(function(){
            window.location.replace('');
          }, 3000);
          
        }else{
          var alertMsg = result.data.DATA;
          alert(alertMsg);
        }
        IndexOverlayFactory.overlayHide();
      });
    }

    $scope.ShowDialogConfirmTopup = false;
    $scope.TopupSuccess = false;
    $scope.Topup = {'to_account' : '4190815076'};
    $scope.ToAccountList = [{'account_no' : '7181187531', 'bank_name' : 'ธนาคารกรุงศรี', 'account_name' : 'จันทิรา งามเลิศสรรพกิจ'},
                            /*{'account_no' : '4048118471', 'bank_name' : 'ธนาคารไทยพานิชย์', 'account_name' : 'บริษัท คาร์โก้ มอลล์ จำกัด'}*/
                            ];

    $scope.HourList = getHourList();
    $scope.MinuteList = getMinuteList();

});
