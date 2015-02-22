(function(){

	var app = angular.module('publication', ['ui.bootstrap']);

	// directive for the filter items
	app.directive('filter', function(){
		return{
			restrict:'E',
			templateUrl:"tpl/filter.tpl.html"
		};
	});

	//directive for the publication items
	app.directive('publicationItem', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/publication-item.tpl.html'
		};
	});

	//directives for the participation items
	app.directive('participationItem', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/participation-item.tpl.html'
		};
	});

	//directive for research events and deadlines
	app.directive('researchEventsAndDeadlines', function(){
		return{
			restrict:'E',
			templateUrl:'tpl/research-events-deadlines.tpl.html'
		};
	});
	
	//directive for generating the user nav bar
	app.directive('userNavBar', function(){
		return{
			restrict: 'E',
			templateUrl:'tpl/nav-bar.tpl.html'
		};
	});

	//controller for the header bar
	app.controller('NavbarController', ['$http', function($http){
		var navbarCtrl = this;

		navbarCtrl.userInfo = [];
		$http.get("ajax/user-nav-bar-info.php").success(function(data){
			navbarCtrl.userInfo = data;
		});

	}]);

	//main controller for the content
	app.controller('PublicationController',['$http', function($http){
		var publicationCtrl = this;
	

		publicationCtrl.filter = [];
		$http.get("ajax/filter.php").success(function(data){
			publicationCtrl.filter = data;
		});

		publicationCtrl.publicationItems = [];
		$http.get("ajax/publication-items.php").success(function(data){
			publicationCtrl.publicationItems = data;
		});	

		publicationCtrl.participationItems = [];
		$http.get("ajax/participation.php").success(function(data){
			publicationCtrl.participationItems = data;
		});
		
		publicationCtrl.researchEventsDeadlines = [];
		$http.get("ajax/research-events-deadlines.php").success(function(data){
			publicationCtrl.researchEventsDeadlines = data;
		});

		publicationCtrl.navBarPublicationItems = [];
		$http.get("ajax/nav-bar-publications.php").success(function(data){
			publicationCtrl.navBarPublicationItems = data;
		});
	}]);
	
})();
