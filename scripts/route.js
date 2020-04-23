angular.module('app').config(function($routeProvider, $locationProvider) {
    $routeProvider
    .when("/", {
        templateUrl : "views/main/main.html",
        controller : "HomeController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/HomeController.js" ]
				});
			} ]
		}
	})

	.when("/forgot-pass/:data_key", {
        templateUrl : "views/update-forgot-pass.html",
        controller : "ForgotPassController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ForgotPassController.js" ]
				});
			} ]
		}
	})

	.when("/home", {
        templateUrl : "views/main/main.html",
        controller : "HomeController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/HomeController.js" ]
				});
			} ]
		}
	})

	.when("/contact-us", {
        templateUrl : "views/contact-us/main.html",
        controller : "ContactUsController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ContactUsController.js" ]
				});
			} ]
		}
	})

	.when("/product-info", {
        templateUrl : "views/product-info/main.html",
        controller : "ProductInfoController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ProductInfoController.js" ]
				});
			} ]
		}
	})

	.when("/view-orders", {
        templateUrl : "views/product-order/view-order.html",
        controller : "ProductOrderController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ProductOrderController.js" ]
				});
			} ]
		}
	})

	.when("/shipping-options", {
        templateUrl : "views/product-order/shipping-options.html",
        controller : "ShippingOptionsController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ShippingOptionsController.js" ]
				});
			} ]
		}
	})
	
	.when("/summary-orders", {
        templateUrl : "views/product-order/summary-order.html",
        controller : "SummaryOrderController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/SummaryOrderController.js" ]
				});
			} ]
		}
	})

	.when("/topup", {
        templateUrl : "views/money-bag/topup.html",
        controller : "TopupInformController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/TopupInformController.js" ]
				});
			} ]
		}
	})

	.when("/pay/:pay_type/:key_id?", {
        templateUrl : "views/money-bag/pay.html",
        controller : "PayController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/PayController.js" ]
				});
			} ]
		}
	})

	.when("/tracking/order", {
        templateUrl : "views/tracking/product-order.html",
        controller : "TrackingOrderController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/TrackingOrderController.js" ]
				});
			} ]
		}
	})

	.when("/tracking/order/detail/:order_id", {
        templateUrl : "views/tracking/detail.html",
        controller : "TrackingOrderDetailController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/TrackingOrderDetailController.js" ]
				});
			} ]
		}
	})

	.when("/importer", {
        templateUrl : "views/importer/main.html",
        controller : "ImporterMainController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ImporterMainController.js" ]
				});
			} ]
		}
	})

	.when("/importer/detail/:id?", {
        templateUrl : "views/importer/detail.html",
        controller : "ImporterDetailController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ImporterDetailController.js" ]
				});
			} ]
		}
	})

	.when("/money-bag", {
        templateUrl : "views/money-bag/main.html",
        controller : "MoneyBagController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/MoneyBagController.js" ]
				});
			} ]
		}
	})

	.when("/condition", {
        templateUrl : "views/other/condition.html",
        controller : "ConditionController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/ConditionController.js" ]
				});
			} ]
		}
	})

	.when("/suggestion", {
        templateUrl : "views/other/suggestion.html",
        controller : "SuggestionController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/SuggestionController.js" ]
				});
			} ]
		}
	})

	.when("/admin", {
        templateUrl : "views/admin/home.html",
        controller : "AdminHomeController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminHomeController.js" ]
				});
			} ]
		}
	})

	.when("/admin/signin", {
        templateUrl : "views/admin/login.html",
        controller : "AdminLoginController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminLoginController.js" ]
				});
			} ]
		}
	})

	.when("/admin/home", {
        templateUrl : "views/admin/home.html",
        controller : "AdminHomeController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminHomeController.js" ]
				});
			} ]
		}
	})

	.when("/admin/order", {
        templateUrl : "views/admin/order/main.html",
        controller : "AdminOrderController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminOrderController.js" ]
				});
			} ]
		}
	})

	.when("/admin/order/detail/:order_id", {
        templateUrl : "views/admin/order/detail.html",
        controller : "AdminOrderDetailController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminOrderDetailController.js" ]
				});
			} ]
		}
	})

	.when("/admin/topup", {
        templateUrl : "views/admin/topup/main.html",
        controller : "AdminTopupController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminTopupController.js" ]
				});
			} ]
		}
	})

	.when("/admin/pay/log", {
        templateUrl : "views/admin/pay/main.html",
        controller : "AdminPayController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminPayController.js" ]
				});
			} ]
		}
	})

	.when("/admin/importer", {
        templateUrl : "views/admin/importer/main.html",
        controller : "AdminImporterController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminImporterController.js" ]
				});
			} ]
		}
	})

	.when("/admin/importer/detail/:importer_id", {
        templateUrl : "views/admin/importer/detail.html",
        controller : "AdminImporterDetailController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminImporterDetailController.js" ]
				});
			} ]
		}
	})

	.when("/admin/exchange-rate", {
        templateUrl : "views/admin/exchange-rate/main.html",
        controller : "AdminExchangeRateController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminExchangeRateController.js" ]
				});
			} ]
		}
	})

	.when("/admin/manage-admin", {
        templateUrl : "views/admin/manage-admin/main.html",
        controller : "AdminManageAdminController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminManageAdminController.js" ]
				});
			} ]
		}
	})

	.when("/admin/manage-admin/detail/:id?", {
        templateUrl : "views/admin/manage-admin/detail.html",
        controller : "AdminManageAdminDetailController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminManageAdminDetailController.js" ]
				});
			} ]
		}
	})

	.when("/admin/transfer", {
        templateUrl : "views/admin/transfer/main.html",
        controller : "AdminTransferController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminTransferController.js" ]
				});
			} ]
		}
	})

	.when("/admin/deposit", {
        templateUrl : "views/admin/deposit/main.html",
        controller : "AdminDepositController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminDepositController.js" ]
				});
			} ]
		}
	})

	.when("/admin/transport-rate", {
        templateUrl : "views/admin/transport-rate/main.html",
        controller : "AdminTransportRateController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminTransportRateController.js" ]
				});
			} ]
		}
	})

	.when("/admin/customer", {
        templateUrl : "views/admin/customer/main.html",
        controller : "AdminCustomerController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminCustomerController.js" ]
				});
			} ]
		}
	})

	.when("/admin/landing-page", {
        templateUrl : "views/admin/landing-page/main.html",
        controller : "AdminLandingPageController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminLandingPageController.js" ]
				});
			} ]
		}
	})

	.when("/admin/landing-page/detail/:id?", {
        templateUrl : "views/admin/landing-page/detail.html",
        controller : "AdminLandingPageDetailController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminLandingPageDetailController.js" ]
				});
			} ]
		}
	})

	.when("/admin/history/refund", {
        templateUrl : "views/admin/history/refund.html",
        controller : "AdminRefundHistoryController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminRefundHistoryController.js" ]
				});
			} ]
		}
	})

	.when("/admin/history/withdrawn", {
        templateUrl : "views/admin/history/withdrawn.html",
        controller : "AdminWithdrawnHistoryController",
        resolve : {
			loadMyCtrl : [ '$ocLazyLoad', function($ocLazyLoad) {
				return $ocLazyLoad.load({
					files : [ "scripts/controllers/admin/AdminWithdrawnHistoryController.js" ]
				});
			} ]
		}
	})

	;
	$locationProvider.html5Mode({
	  enabled: true,
	  requireBase: true
	});
	// $locationProvider.hashPrefix('');
	
});
