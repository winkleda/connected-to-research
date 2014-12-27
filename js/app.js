(function(){

	var app = angular.module('publication', []);

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

	app.controller('PublicationController',['$http', function($http){
		var publicationCtrl = this;
	

		publicationCtrl.filter = [];
		$http.get("test/filter.json").success(function(data){
			publicationCtrl.filter = data;
		});

		publicationCtrl.publicationItems = [];
		$http.get("test/publication-items.json").success(function(data){
			publicationCtrl.publicationItems = data;
		});	

		publicationCtrl.participationItems = [];
		$http.get("test/participation.json").success(function(data){
			publicationCtrl.participationItems = data;
		});

	}]);
	
})();
